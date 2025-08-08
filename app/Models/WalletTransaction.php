<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WalletTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "wallet_id",
        "amount",
        "type",
        "description",
        "reference_type",
        "reference_id",
        "meta_data",
    ];

    protected $casts = [
        'meta_data' => 'array',
    ];

    /**
     * Get the wallet that owns the transaction
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
