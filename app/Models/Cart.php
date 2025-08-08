<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','item_id','item_type','status','total_price'];

    public function cart_items()
    {
        return $this->hasMany(CartItem::class,'cart_id');
    }
}
