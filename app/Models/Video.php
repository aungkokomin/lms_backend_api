<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Video extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title','description','video_url','video_duration','status'];

    // public function videoMorph()
    // {
    //     return $this->morphTo();
    // }

    public function thumbnail()
    {
        return $this->morphOne(Image::class,'imageable');
    }
}
