<?php

namespace App\Repositories;

use App\Interfaces\CartRepositoryInterface;
use App\Models\Cart;
use App\Models\CartItem;
use Dflydev\DotAccessData\Exception\DataException;

class CartRepository implements CartRepositoryInterface
{
    protected $cart;

    public function __construct(Cart $cart) {
        $this->cart = $cart;
    }
    // Add your repository methods here
    public function getCartDataByUser($user_id)
    {
        try {
            return $this->cart->with('cart_items')->where('user_id',$user_id)->where('status','active')->firstOrFail();
        } catch (\Exception $e) {
            throw new DataException();
        }
    }

    public function getCart($id)
    {
        return $this->cart->findOrFail($id);
    }

    public function create($user_id)
    {
        return $this->cart->firstOrCreate([
            'user_id' => $user_id,
            'status' => 'active',
        ]);
    }
    public function edit($user_id)
    {

    }

    public function delete($id)
    {
        try{
            $cart = $this->cart->findOrFail($id);
            if($cart){
                $cart->courses()->detach();
                return $cart->delete();
            }
        }catch(\Exception $e){
            throw new DataException($e->getMessage());
        } 
    }

    public function deleteAllCart($user_id)
    {
        try{
            // $cart = $this->getCartDataByUser($user_id);
            $carts = $this->cart->where('user_id',$user_id)->get();

            foreach($carts as $cart){
                if($cart){
                    $cart->cart_items()->delete();
                    $cart->delete();
                }
            }

            return true;
        }catch(\Exception $e){
            throw new DataException($e->getMessage());
        }
    }

    public function addCartItems(array $data)
    {
        $cartItem = CartItem::where('cart_id',$data['cart_id'])->where('course_id',$data['course_id'])->first();
        if($cartItem){
            $cartItem->update([
                'quantity' => $cartItem->quantity,
                'net_price' => $cartItem->net_price + $data['course_price']
            ]);
        }else{
            $cartItem = CartItem::firstOrCreate([
                'cart_id' => $data['cart_id'],
                'course_id' => $data['course_id'],
                'course_price' => $data['course_price'],
                'net_price' => $data['course_price'],
                'quantity' => 1
            ]);
        }
        
        return $cartItem;
    }

    public function minusCartItems($cartItem)
    {
        $cartItem = CartItem::where('cart_id',$cartItem->cart_id)->where('itemable_id',$cartItem->item_id)->where('itemable_type',$cartItem->item_type)->first();
        if($cartItem){
            return $cartItem->delete();
        }
        return true;
        // if($cartItem){
        //     if(($cartItem->quantity - 1) > 0){
        //         $cartItem->update([
        //             'quantity' => $cartItem->quantity
        //         ]);
        //     }else{
        //     }
        // }
    }
}