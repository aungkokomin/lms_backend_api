<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quiz extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable=['lesson_id','title','description','passing_score','time_limit'];

    /**
     * Summary of questions
     * @return HasMany
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function answers()
    {
        return $this->hasManyThrough(Answer::class,Question::class,'quiz_id','question_id');
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class)->withTrashed();
    }
}
