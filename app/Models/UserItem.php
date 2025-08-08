<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserItem extends Model
{
    use HasFactory;

    protected $table = 'user_items';
    protected $fillable = ['user_id','itemable_id','itemable_type','order_id'];
}
