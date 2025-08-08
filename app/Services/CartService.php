<?php

namespace App\Services;

use App\Interfaces\CartRepositoryInterface;
use App\Interfaces\OrderRepositoryInterface;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Course;
use App\Models\CoursePurchase;
use App\Models\Module;
use App\Models\Order;
use App\Models\Payment;
use App\Models\StripeProduct;
use App\Models\UserGrantCode;
use App\Models\UserItem;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\Coupon;
use Stripe\PaymentIntent;
use Stripe\Stripe;
use Stripe\StripeClient;

use function App\Helpers\storeFile;

class CartService
{
    protected $cartRepositoryInterface;
    protected $order;


    public function __construct(CartRepositoryInterface $cartRepositoryInterface,OrderRepositoryInterface $order) {
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->order = $order;
    }
    
    /**
     * Summary of addToCart
     * @param array $data
     * @throws \Exception
     * @return mixed
     */
    public function addToCart(array $data)
    {
        try {
            //code...
            $items = json_decode($data['items']);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format');
            }

            $user_id = Auth::user()->id;
            foreach ($items as $item) {
                $userItemExist = UserItem::where('user_id',$user_id)->where('itemable_id',$item->item_id)->where('itemable_type',$item->item_type)->exists();
                if($userItemExist){
                    throw new Exception('Item already purchased');
                }
                if($item->item_type == 'course'){
                    $course = Course::findOrFail($item->item_id);
                    $item->item_price = $course->price;
                    $item->net_price = $course->price * $item->quantity;
                    
                }else if($item->item_type == 'module'){
                    $module = Module::findOrFail($item->item_id);
                    $item->item_price = $module->price;
                    $item->net_price = $module->price * $item->quantity;
                    
                }else if($item->item_type == 'bundle'){
                    $bundle = Bundle::findOrFail($item->item_id);
                    $item->item_price = $bundle->price;
                    $item->net_price = $bundle->price * $item->quantity;
                    
                }
                $cart = $this->cartRepositoryInterface->create($user_id);
                $cart->cart_items()->updateOrCreate([
                    'cart_id' => $cart->id,
                    'quantity' => $item->quantity,
                    'itemable_type' => $item->item_type,
                    'itemable_id' => $item->item_id,
                    'item_price' => $item->item_price,
                    'net_price' => $item->net_price,
                ]);
            }

            if($cart->cart_items()->exists()){
                $totalprice = $cart->cart_items()->sum('net_price');
                $cart->update([
                    'total_price' => $totalprice
                ]);
            }
            return $cart;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
            
        }
    }

    /**
     * Summary of removeCart
     * @param mixed $id
     * @throws \Exception
     * @return mixed
     */
    public function removeCart($data)
    {
        try {
            $user = Auth::user();
            $cart = $this->cartRepositoryInterface->getCartDataByUser($user->id);
            $data->cart_id = $cart->id;
            $result = $this->cartRepositoryInterface->minusCartItems($data);
            if(!$cart->cart_items()->exists()){
                $cart->delete();
            }else{
                $totalprice = $cart->cart_items()->sum('net_price');
                $cart->update([
                    'total_price' => $totalprice
                ]);
            }
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Summary of getShoppingCart
     * @param mixed $user_id
     * @throws \Exception
     * @return mixed
     */
    public function getShoppingCart($user_id){
        try {
            $cart = $this->cartRepositoryInterface->getCartDataByUser($user_id);
            $cart->cart_items->map(function($item){
                if($item->itemable_type == 'course'){
                    $course = Course::findOrFail($item->itemable_id);
                    $item->course = $course;
                }else if($item->itemable_type == 'module'){
                    $module = Module::findOrFail($item->itemable_id);
                    $item->module = $module;
                }else if($item->itemable_type == 'bundle'){
                    $bundle = Bundle::findOrFail($item->itemable_id);
                    $item->bundle = $bundle;
                }
            });
            return $cart;
        } catch (\Exception $e) {
            //throw $th;
            throw new Exception($e->getMessage());
        }
    }
    
    /**
     * Summary of clearCart
     * @param mixed $user_id
     * @throws \Exception
     * @return mixed
     */
    public function clearCart($user_id) {
        try {
            return $this->cartRepositoryInterface->deleteAllCart($user_id);
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Summary of checkout
     * @param mixed $data
     * @throws \Exception
     * @return mixed
     */
    public function checkout(array $data)
    {
        try {
            $user = Auth::user();
            
            $cart = $this->cartRepositoryInterface->getCartDataByUser($user->id);
            
            if(!$cart->count()){
                throw new Exception('No cart for user');
            }
            
            $cartItems = CartItem::where('cart_id', $cart->id)->get();

            if(!$cartItems->count()){
                throw new Exception('empty cart');
            }

            $lineItems = [];
            foreach($cartItems as $item){
                $order_items [] = [
                    'itemable_id' => $item->itemable_id,
                    'itemable_type' => $item->itemable_type,
                    'quantity' => $item->quantity,
                    'item_price' => $item->item_price,
                    'net_price' => $item->net_price
                ];

                $userItemExist = UserItem::where('user_id',$user->id)->where('itemable_id',$item->itemable_id)->where('itemable_type',$item->itemable_type)->exists();
                if($userItemExist){
                    throw new Exception('Item already purchased');
                }
                
                if($data['payment_method'] == 'stripe'){
                    if($item->itemable_type == 'course'){
                        $stripeProduct = Course::findOrFail($item->itemable_id)->stripeProduct()->first();
                    }else if($item->itemable_type == 'module'){
                        $stripeProduct = Module::findOrFail($item->itemable_id)->stripeProduct()->first();
                    }
    
                    $stripe = new StripeClient(config('services.stripe.secret'));
                    if($stripeProduct){
                        $lineItems [] = [
                            'price' => $stripe->products->retrieve($stripeProduct->stripe_product_id)->default_price,
                            'quantity' => $item->quantity,
                        ];
                    }
                    if(isset($data['grant_code'])){
                        $check = UserGrantCode::where('code',$data['grant_code'])->where('grant_type','stripe')->first();
                        if(!$check && $stripe->coupons->retrieve($data['grant_code'])){
                            throw new Exception('This grant code cannot be used for stripe payment.');
                        }
                        $grant_code = [['coupon' => $data['grant_code']]];
                    }else{
                        $grant_code = [];
                    }
                }
            }


            // Order Process
            $order = $this->order->create([
                'order_uid' => uniqid(),
                'user_id' => $cart->user_id,
                'status' => Order::STATUS_PENDING,
                'order_price' => $cart->total_price,
                'order_items' => json_encode($order_items),
                'order_type' => Order::TYPE_PURCHASE,
                'order_date' => date('Y-m-d H:m:i')
            ]);
            Log::info("Order Process: ".$order->order_uid);

            if(isset($data['grant_code'])){
                $grantedPrice = $this->calculateGrantedAmt($data['grant_code'],$cart->total_price);
            }else{
                $grantedPrice = null;
            }

            // Payment Process
            if($data['payment_method'] == 'stripe'){
                $stripeSecret = config('services.stripe.secret');
                // Stripe payment gateway integration
                Stripe::setApiKey($stripeSecret);
                
                if(config('app.url') == 'http://localhost'){
                    $session = Session::create([
                        'payment_method_types' => ['card'],
                        'customer_email' => Auth::user()->email,
                        'line_items' => $lineItems,
                        'discounts' => $grant_code,
                        'mode' => 'payment',
                        'expires_at' => time() + 1800,
                        // 'success_url' => config('app.url').':8000/payment/stripe/success?session_id={CHECKOUT_SESSION_ID}&order_id='.$order->order_uid,
                        'success_url' => config('app.url').':8000/api/stripe/success?payment-type=purchase',
                        'cancel_url' => config('app.url').':8000/api/stripe/cancel?payment-type=purchase&session_id={CHECKOUT_SESSION_ID}',
                    ]);
                }else{
                    $session = Session::create([
                        'payment_method_types' => ['card','grabpay','link'],
                        'customer_email' => Auth::user()->email,
                        'line_items' => $lineItems,
                        'discounts' => $grant_code,
                        'mode' => 'payment',
                        'expires_at' => time() + 1800,
                        'success_url' => config('services.frontend.url').'/payment/stripe/success??payment-type=purchase',
                        'cancel_url' => config('services.frontend.url').'/payment/stripe/cancel?payment-type=purchase&session_id={CHECKOUT_SESSION_ID}',
                    ]);
                }

                $savePayment = [
                    'status' => Payment::STATUS_PENDING,
                    'amount' => $session->amount_total / 100,
                    'discounted_fee' => $grantedPrice ?? 0,
                    'is_discounted' => $grantedPrice ? 'true' : 'false',
                    'grant_code' => isset($data['grant_code']) ?? null,
                    'payment_method' => 'stripe',
                    'payment_reference' => $session->id,
                    'currency' => $session->currency,
                    'transaction_id' => 'stripe_'.uniqid(),
                    'payment_details' => null,
                    'payment_date' => null,
                ];
                $payment = Payment::create($savePayment);
                $order->update([
                    'transaction_id' => $payment->transaction_id
                ]);
                
                if(isset($data['grant_code'])){
                    UserGrantCode::where('code',$data['grant_code'])->update([
                        'used_by' => $payment->transaction_id,
                    ]);
                }

                return [
                    'url' => $session->url,
                ];
            }else if($data['payment_method'] == 'crypto'){

                $payment = Payment::create([
                    'status' => Payment::STATUS_PENDING,
                    'amount' => $grantedPrice ? $order->order_price - $grantedPrice : $order->order_price,
                    'discounted_fee' => $grantedPrice ?? 0,
                    'is_discounted' => $grantedPrice ? 'true' : 'false',
                    'grant_code' => isset($data['grant_code']) ?? null,
                    'payment_method' => $data['payment_method'],
                    'transaction_id' => "crypto_".uniqid(),
                    'payment_date' => now()
                ]);

                Log::info("Payment Process: ".$payment->transaction_id);

                if($payment){
                    if($payment){
                        if(isset($data['crypto_payment_image'])){
                            $paths = storeFile($data['crypto_payment_image'],'crypto_payments');
                            $payment->image()->create([
                                'url' => $paths['url']
                            ]);
                        }
        
                        $order->update([
                            'transaction_id' => $payment->transaction_id,
                        ]);
                    }
                }else{
                    throw new Exception('Payment Process Failed');
                }

                return $payment;
            }

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Summary of bindOrderItemstoUser
     * @param mixed $order
     * @throws \Exception
     * @return mixed
     */
    public function bindOrderItemstoUser($order){
        try {
            $orderItems = json_decode($order->order_items);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Invalid JSON format');
            }
            
            foreach($orderItems as $item){
                $result[] = UserItem::create([
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'itemable_id' => $item->itemable_id,
                    'itemable_type' => $item->itemable_type,
                ]);
            }
            Log::info("Order Items: ".$order->order_items."");
            return $result;
        } catch (\Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Summary of applyGrantToOrder
     * @param mixed $grant_code
     * @param mixed $total_price
     * @throws \Exception
     * @return mixed
     */
    public function calculateGrantedAmt($grant_code,$total_price){
        $user_id = Auth::user()->id;
        $grantCode = UserGrantCode::where('code',$grant_code)->first();

        if($grantCode->used_at != null){
            throw new Exception('Grant Code Already Used');
        }else if($grantCode->expired_at < now()){
            throw new Exception('Grant Code Expired');
        }else if($grantCode->is_active == 0){
            throw new Exception('Grant Code Inactive');
        }else if(!$grantCode->count()){
            throw new Exception('Invalid Grant Code');
        }
        // Grant Code Discount
        $reducePercentage = $grantCode->grant_amount ?? UserGrantCode::DEFAULT_GRANT_AMOUNT;
        $reducedPrice = $total_price * ($reducePercentage / 100);
        
        // Grant Code Usage
        // $grantCode->update([
        //     'used_at' => now(),
        //     'used_by' => $user_id
        // ]);

        return $reducedPrice;
    }
}