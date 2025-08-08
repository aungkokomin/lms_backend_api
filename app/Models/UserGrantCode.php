<?php

namespace App\Models;

use Dotenv\Util\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGrantCode extends Model
{
    use HasFactory;

    protected $table = 'user_grant_codes';
    protected $fillable = [
        'student_id',
        'code',
        'grant_type',
        'grant_amount',
        'used_at',
        'used_by',
        'expired_at',
        'is_active'
    ];

    const GRANT_TYPE_STRIPE = 'stripe';
    const GRANT_TYPE_CRYPTO = 'crypto';

    const STATUS_ACTIVE = 'Grant Code is valid';
    const STATUS_INACTIVE = 'Grant Code is invalid';
    const STATUS_USED = 'Grant Code is invalid';
    const STATUS_EXPIRED = 'Grant Code is expired';
    const DEFAULT_GRANT_AMOUNT = 50;
    // const DEFAULT_STRIPE_COUPON_CODE = 'ha172ABe';
    public static function getStatuses()
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_INACTIVE,
            self::STATUS_USED,
            self::STATUS_EXPIRED
        ];
    }
}
