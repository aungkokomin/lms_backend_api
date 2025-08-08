<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService) {
        $this->cartService = $cartService;
    }

    public function addToCart(Request $request)
    {
        try{
            return response()->json([
                'data' => $this->cartService->addToCart($request->all()),
                'status' => 201
            ],201);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function removeCart(Request $request)
    {
        try{
            $data = json_decode($request->item);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON format');
            }

            return response()->json([
                'data' => $this->cartService->removeCart($data),
                'status' => 200 
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function getShoppingCart(){
        try {
            $user_id = Auth::user()->id;
            return response()->json([
                'data' => new CartResource($this->cartService->getShoppingCart($user_id)),
                'status' => 200,
            ],200);
        } catch (\Exception $e) {
            //throw $th;
            if(!$e->getMessage()){
                return response()->json([
                    'data' => ["cart_items"=>[]],
                    'status' => 200,
                ],200);
            }else{
                return response()->json([
                    'data' => $e->getMessage(),
                    'status' => 200,
                ],200);
            }
        }
    }

    public function clearCart()
    {
        try {
            $user_id = Auth::user()->id;
            return response()->json([
                'data' => $this->cartService->clearCart($user_id),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function checkout(Request $request)
    {
        try {
            $data = $request->validate([
                'payment_method' => 'string|in:stripe,crypto',
                'grant_code' => 'sometimes|string|max:255|exists:user_grant_codes,code,used_at,NULL,is_active,1',
                'payment_method_id' => 'sometimes|string|max:255|nullable',
                'crypto_payment_image' => 'sometimes|image|mimes:jpeg,png,jpg,gif,svg|max:2048|nullable',
            ]);

            return response()->json([
                'data' => $this->cartService->checkout($data),
                // 'data' => $session,
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }
}
