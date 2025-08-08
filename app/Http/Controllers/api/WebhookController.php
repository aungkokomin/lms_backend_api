<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Affiliator;
use App\Models\Cart;
use App\Models\Commission;
use App\Notifications\StudentRegistrationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\Webhook;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Referral;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGrantCode;
use App\Models\UserItem;
use App\Notifications\OrderSuccessNotification;

use function App\Helpers\affiliateCommission;
use function App\Helpers\assignUserRole;

class WebhookController extends Controller
{
    //
    public function handleWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }
        Log::info('Event Type : '.$event->type);
        // Handle the event
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->handleCheckoutSessionCompleted($session);
                break;
            case 'checkout.session.expired':
                $session = $event->data->object;
                $this->handleCheckoutSessionExpired($session);
                break;
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                $this->handlePaymentIntentSucceeded($paymentIntent);
                break;
            // Add more event types as needed
            default:
                Log::info('Received unknown event type ' . $event->type);
        }

        return response()->json(['status' => 'success'], 200);
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        Log::info('Checkout Session Json Obj | ');
        Log::info('Session ID : '.$session->id);
        // Retrieve the order by session ID
        $payment = Payment::where('payment_reference',$session->id)->where('status',Payment::STATUS_PENDING)->first();
        // Log::info('Payment Obj : '.$payment);
        if ($payment) {
            $payment->update([
                'status' => Payment::STATUS_COMPLETED,
                'payment_reference' => $session->payment_intent,
                'payment_date' => now()
            ]);

            UserGrantCode::where('used_by',$payment->transaction_id)->update([
                'used_at' => now(),
                'is_active' => false
            ]);

            $order = Order::where('transaction_id',$payment->transaction_id)->where('status',Order::STATUS_PENDING)->first();

            if($order){
                // Update the order status
                $order->update([
                    'status' => Order::STATUS_COMPLETED,
                    'completed_at' => now()
                ]);
                Log::info('Order Updated: ' . $order->order_type);
                if($order->order_type == Order::TYPE_PURCHASE){
                    $orderItems = json_decode($order->order_items);
                    
                    // Attach the order items to the user
                    foreach($orderItems as $item){
                        $userItem = UserItem::create([
                            'user_id' => $order->user_id,
                            'itemable_id' => $item->itemable_id,
                            'itemable_type' => $item->itemable_type,
                            'order_id' => $order->id,
                            'purchase_date' => now()
                        ]);
    
                        Log::info('User Item ID: ' . $item->itemable_id . ' - Item Type: ' . $item->itemable_type);
                        if(!$userItem){
                            throw new \Exception('User Item Creation Failed');
                        }
                    }
                    Log::info('Order Updated: ' . $order->transaction_id);
                    $user = User::find($order->user_id);
                    if($user){
                        // Notify the user
                        $message = 'Your order ' . $order->transaction_id . ' has been successfully completed.';
                        $user->notify(new OrderSuccessNotification($payment , $user->id));
                    }
                    // Clear the cart
                    $cart = Cart::where('user_id',$order->user_id)->first();
                    if($cart){
                        $cart->cart_items()->delete();
                        $cart->delete();
                    }
                }else if($order->order_type == Order::TYPE_REGISTER){
                    
                    // Assign the user role
                    $user = User::find($order->user_id);
                    Log::info('User Updated: ' . $user->name.'|'.$user->id.'|'.Role::ROLE_STUDENT);
                    assignUserRole($order->user_id,Role::ROLE_STUDENT);
                    $student = $user->student;
                    if($user->hasRole(Role::ROLE_GUEST)){
                        $user->removeRole(Role::ROLE_GUEST);
                    }
                    // Notify the user
                    $message = 'Welcome to the Maaledu. You have successfully registered as a student. You can now access the courses and modules.';
                    $user->notify(new StudentRegistrationNotification($message,$user,$student));
                    // Mail::to($user->email)->send(new StudentRegistration($user->name));
                    $updatedUser = $user->with('roles','student')->find($user->id);
                    Log::info('User Updated: ' . $updatedUser->name.'|'.$updatedUser->id);
                    affiliateCommission($updatedUser->id, $payment->amount);
                }

            }
        }
    }

    protected function handleCheckoutSessionExpired($session)
    {
        // Handle the checkout session expired event
        // You can update the order status or perform other actions here
        Log::info('Checkout Session Expired | ');
        Log::info('Session ID : '.$session->id);

        $payment = Payment::where('payment_reference',$session->id)->where('status',Payment::STATUS_PENDING)->first();

        if($payment){
            $payment->update(['status' => Payment::STATUS_REJECTED]);
            $order = Order::where('transaction_id',$payment->transaction_id)->where('status',Order::STATUS_PENDING)->first();
            if($order){
                $order->update(['status' => Order::STATUS_REJECTED]);
            }
        }
    }
    protected function handlePaymentIntentSucceeded($paymentIntent)
    {
        // Handle the payment intent succeeded event
        // You can update the order status or perform other actions here
        Log::info('Payment Intent Json Obj | ');
        Log::info($paymentIntent->id);
        if($paymentIntent){
            Payment::where('payment_reference',$paymentIntent->id)->update([
                'payment_details' => $paymentIntent
            ]);
        }
    }

}
