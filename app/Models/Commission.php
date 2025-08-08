<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = ['user_id','referral_id','commission_amount','description','applied_commission_rate','used_own_commission_rate','payment_amount'];

    /**
     * Summary of hidden
     * @var array
     */
    protected $hidden = [];

    const DEFAULT_COMMISSION_RATE = 5;
    
    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id')->withTrashed();
    }

    public function referral() : BelongsTo
    {
        return $this->belongsTo(Referral::class,'referral_id','referrer_id');
    }

    public function affiliate() : BelongsTo
    {
        return $this->belongsTo(Affiliator::class,'referral_id','referral_id');
    }
}
