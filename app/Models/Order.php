<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['order_uid','user_id','status','transaction_id','order_price','order_items','payment_method','order_date','order_type'];

    const STATUS_PENDING = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_EXPIRED = 'expired';

    const TYPE_PURCHASE = 'item_purchase';
    const TYPE_REGISTER = 'register';
    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_REJECTED,
        ];
    }

    public static function getOrderTypes()
    {
        return [
            self::TYPE_PURCHASE,
            self::TYPE_REGISTER,
        ];
    }
    
    public function user(){
        return $this->belongsTo(User::class);
    }

    public function payment(){
        return $this->hasMany(Payment::class,'transaction_id','transaction_id');
    }
    
}
