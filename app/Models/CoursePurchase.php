<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoursePurchase extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','course_id','transaction_id','total_amount','purchased_date','status','payment_method'];
}
