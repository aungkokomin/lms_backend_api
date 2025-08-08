<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bundle extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'thumbnail',
        'status',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'bundle_courses', 'bundle_id', 'course_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class,'imageable');
    }
}
