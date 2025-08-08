<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentQuiz extends Model
{
    use HasFactory;

    protected $fillable = ['student_id','quiz_id','score','attempted_questions','status','attempt_date'];

    public function student()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    /**
     * Get the user that owns the StudentQuiz
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quiz(): BelongsTo
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }  
}
