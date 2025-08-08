<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lesson extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title','contents','module_id','video_id','status','sorting_order'];

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class,'video_id');
    }

    public function quiz()
    {
        return $this->hasMany(Quiz::class,'lesson_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class,'model_has_image');
    }

    // public function videos()
    // {
    //     return $this->morphOne(Video::class,'model_has_video');
    // }
}
