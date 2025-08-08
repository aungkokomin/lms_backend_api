<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionSetting extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Summary of fillable
     * @var array
     */
    protected $fillable = [
        'name',
        'commission_rate',
        'description'
    ];
}
