<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    use HasFactory;

    protected $table = 'lesson_progress';

    protected $fillable = [
        'lesson_id',
        'student_id',
        'completed',
        'completed_at',
        'last_accessed_at',
        'created_at',
        'updated_at',
    ];

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class);
    }
    
}
