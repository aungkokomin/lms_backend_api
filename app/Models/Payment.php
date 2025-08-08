<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory;

    protected $table = 'payments';

    protected $fillable = ['payment_method','transaction_id','amount','status','user_id','currency','payment_reference','payment_date','payment_details','discounted_fee','is_discounted','grant_code'];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REFUNDED = 'refunded';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_COMPLETED,
            self::STATUS_REJECTED,
            self::STATUS_REFUNDED,
        ];
    }
    
    public function order()
    {
        return $this->belongsTo(Order::class,'transaction_id','transaction_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class,'imageable');
    }
}
