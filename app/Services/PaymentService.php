<?php

namespace App\Services;

use App\Interfaces\PaymentRepositoryInterface;
use App\Mail\StudentRegistration;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Role;
use App\Models\User;
use App\Models\UserGrantCode;
use App\Models\UserItem;
use App\Notifications\OrderSuccessNotification;
use App\Notifications\StudentRegistrationNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Stripe\Checkout\Session;
use Stripe\PaymentIntent;
use Stripe\Stripe;

use function App\Helpers\affiliateCommission;
use function App\Helpers\assignUserRole;
use function App\Helpers\storeFile;

class PaymentService
{
    protected $paymentRepositoryInterface;

    public function __construct(PaymentRepositoryInterface $paymentRepositoryInterface) {
        $this->paymentRepositoryInterface = $paymentRepositoryInterface;
    }
    // Add your repository methods here

    public function list()
    {   
        return $this->paymentRepositoryInterface->list();
    }

    public function show($id)
    {
        return $this->paymentRepositoryInterface->show($id);
    }

    public function confirmPayment($data)
    {
        // Validate the data before processing
        $validator = Validator::make($data, [
            'payment_id' => 'required|exists:payments,id',
            'user_id' => 'sometimes|exists:users,id'
        ]);

        if($validator->fails()){
            throw new \Exception($validator->errors());
        }

        // Confirm the payment 
        $payment = $this->paymentRepositoryInterface->confirm($data);

        if($payment){
            $user = User::findOrFail($data['user_id']);
            $order = Order::where('transaction_id',$payment->transaction_id)->first();

            UserGrantCode::where('used_by',$payment['transaction_id'])->update([
                'used_at' => now(),
                'is_active' => false
            ]);

            if(!$order->payment()->where('status','!=',Payment::STATUS_COMPLETED)->count()){
                $order->update(['status' => Order::STATUS_COMPLETED]);
            }
            
            // Assign the user role based on the order type
            if($order->order_type == Order::TYPE_REGISTER){
                
                // Assign the user role
                assignUserRole($user->id,Role::ROLE_STUDENT);
                $user->removeRole(Role::ROLE_GUEST);
                $student = $user->student()->orderByDesc('id')->first();
                // Notify the user
                $message = "Welcome to the Maaledu Platform. You have successfully registered as a student. You can now access the courses and modules.";
                $user->notify(new StudentRegistrationNotification($message,$user,$student));
                affiliateCommission($user->id,$payment->amount);
                // Mail::to($user->email)->send(new StudentRegistration($user->name));
            }else if ($order->order_type == Order::TYPE_PURCHASE){
                $order_items = json_decode($order->order_items);
                foreach ($order_items as $item) {

                    $userItemExist = UserItem::where('user_id',$order->user_id)
                    ->where('itemable_id',$item->itemable_id)
                    ->where('itemable_type',$item->itemable_type)
                    ->exists();

                    if($userItemExist){
                        continue;
                    }

                    $userItem = UserItem::create([
                        'user_id' => $order->user_id,
                        'itemable_id' => $item->itemable_id,
                        'itemable_type' => $item->itemable_type,
                        'order_id' => $order->id,
                        'purchase_date' => now()
                    ]);
                }

                // Notify the user
                $message = "Your order " . $order->transaction_id . " has been successfully completed.";
                $user->notify(new OrderSuccessNotification($payment, $order->user_id));
                // Mail::to($user->email)->send(new StudentRegistration($user->name));
            }
        }
        return $payment;
    }

    /**
     * Summary of reject
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function reject($data)
    {
        $validator = Validator::make($data, [
            'payment_id' => 'required|exists:payments,id'
        ]);

        if($validator->fails()){
            throw new \Exception($validator->errors());
        }

        $payment = $this->paymentRepositoryInterface->showPendingPayment($data['payment_id']);

        if($payment){
            if($payment->update(['status' => Payment::STATUS_REJECTED])){
                $order = Order::where('transaction_id',$payment->transaction_id)->first();
                if($order){
                    $order->update(['status' => Order::STATUS_REJECTED]);
                }
                $payment->fresh();
            }
        }

        return $payment;
    }

    /**
     * Summary of studentEnrollPayments
     * @param array $data
     * @return mixed
     * @throws \Exception
     */
    public function studentEnrollPayments($data)
    {
        $validator = Validator::make($data, [
            'payment_method' => 'required|in:stripe,crypto',
            'amount' => 'required|numeric',
            'currency' => 'required|string',
            // 'payment_method_id' => 'required_if:payment_method,stripe',
            'crypto_payment_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validator->fails()){
            throw new \Exception($validator->errors());
        }

        // Get the stripe secret key from the config
        $stripeSecret = config('services.stripe.secret');

        // Log the value for debugging
        Log::info('Stripe Secret from config: ' . $stripeSecret);

        if (!$stripeSecret) {
            throw new \Exception('Stripe secret key is not set.');
        }

        $authUser = Auth::user();
        if (!$authUser) {
            throw new \Exception('User is not authenticated.');
        }

        if($authUser->hasRole(Role::ROLE_STUDENT)){
            throw new \Exception('User is already a student.');
        }

        $user = User::find($authUser->id);

        $order = Order::create([
            'order_uid' => uniqid(),
            'user_id' => $user->id,
            'status' => Order::STATUS_PENDING,
            'order_price' => $data['amount'] / 100,
            'order_type' => Order::TYPE_REGISTER,
            'order_date' => date('Y-m-d H:m:i')
        ]);
        
        if(!$order){
            throw new \Exception('Order Process Failed');
        }

        if($data['payment_method'] == 'stripe'){

            // Stripe payment gateway integration
            // Stripe::setApiKey($stripeSecret);

            try {
                $stripe = new \Stripe\StripeClient($stripeSecret);
                if(config('app.env') == 'local'){
                    $success_url = 'http://localhost:8000/payment/stripe/success?payment-type=enroll?session_id={CHECKOUT_SESSION_ID}';
                    $cancel_url = 'http://localhost:8000/payment/stripe/cancel?payment-type=enroll?session_id={CHECKOUT_SESSION_ID}';
                }else{
                    $success_url = config('services.frontend.url').'/payment/stripe/success?payment-type=enroll';
                    $cancel_url = config('services.frontend.url').'/payment/stripe/cancel?payment-type=enroll?session_id={CHECKOUT_SESSION_ID}';
                }

                $session = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price_data' => [
                            'currency' => $data['currency'],
                            'product_data' => [
                                'name' => 'Student Enrollment'
                            ],
                            'unit_amount' => $data['amount'] * 100,
                        ],
                        'quantity' => 1,
                    ]],
                    'mode' => 'payment',
                    'expires_at' => strtotime('+30 minute'),
                    'success_url' => $success_url ?? '',
                    'cancel_url' => $cancel_url ?? ''
                ]);

                Log::info('Checkout Session ID: ' . $session->id);

                $payment = Payment::create([
                    'status' => Payment::STATUS_PENDING,
                    'amount' => $data['amount'],
                    'currency' => $data['currency'],
                    'payment_method' => $data['payment_method'],
                    'payment_reference' => $session->id,
                    'transaction_id' => "stripe_".uniqid(),
                    'payment_date' => now()
                ]);
                $order->update([
                    'transaction_id' => $payment->transaction_id,
                ]);
                
                return [
                    'url' => $session->url,
                ];
            } catch (\Exception $e) {
                throw new \Exception('Stripe Payment Failed: ' . $e->getMessage());
            }

        }else if($data['payment_method'] == 'crypto'){
            // Paypal payment gateway integration
            $payment = Payment::create([
                'status' => Payment::STATUS_PENDING,
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'transaction_id' => "crypto_".uniqid(),
                'payment_date' => now()
            ]);

            // Store the payment image
            if($payment){
                if($data['crypto_payment_image']){
                    $paths = storeFile($data['crypto_payment_image'],'crypto_payments');
                    $payment->image()->create([
                        'url' => $paths['url']
                    ]);
                }

                $order->update([
                    'transaction_id' => $payment->transaction_id,
                ]);
            }

            if(!$payment){
                throw new \Exception('Payment Process Failed');
            }

            return $user;
        } else {
            throw new \Exception('Invalid Payment Method');
        } 
    }

    public function stripeCancel($data){

        $payment = Payment::where('payment_reference',$data['session_id'])->first();

        if($payment){
            $result =  $payment->update(['status' => Payment::STATUS_REJECTED]);
            $order = Order::where('transaction_id',$payment->transaction_id)->first();
            if($order){
                $order->update(['status' => Order::STATUS_REJECTED]);
            }
        }

        return $result;
    }
}