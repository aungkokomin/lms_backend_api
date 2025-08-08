<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $table = 'cart_items';
    protected $fillable = ['cart_id','itemable_id','itemable_type','item_price','net_price','quantity'];

    // public function itemable()
    // {
    //     return $this->morphTo();
    // }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
    
}
