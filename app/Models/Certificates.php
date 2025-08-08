<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificates extends Model
{
    use HasFactory;

    protected $table = 'certifications';
    protected $fillable = [
        'name',
        'description',
        'file',
        'user_id',
    ];


}
