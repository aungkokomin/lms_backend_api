<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeProduct extends Model
{
    use HasFactory;

    protected $table = 'stripe_products';

    protected $fillable = [
        'stripe_product_id',
        'item_id',
        'item_type'
    ];
}
