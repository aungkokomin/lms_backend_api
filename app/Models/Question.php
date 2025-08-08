<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Question extends Model
{
    use HasFactory;

    protected $fillable = ['quiz_id','question_text','question_type'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class,'quiz_id');
    }

    /**
     * Get all of the answer for the Question
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id');
    }
}
