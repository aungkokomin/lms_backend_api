<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGrading extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'student_grading';
    protected $fillable = ['student_id','module_id','grade','remarks','gpa_score'];

    const GRADE_A = "A";
    const GRADE_A_MINUS = "A-";
    const GRADE_B_PLUS = "B+";
    const GRADE_B = "B";
    const GRADE_B_MINUS = "B-";
    const GRADE_C_PLUS = "C+";
    const GRADE_C = "C";
    const GRADE_C_MINUS = "C-";
    const GRADE_D = "D";
    const GRADE_F = "F";

    public static $grades = [
        self::GRADE_A,
        self::GRADE_A_MINUS,
        self::GRADE_B_PLUS,
        self::GRADE_B,
        self::GRADE_B_MINUS,
        self::GRADE_C_PLUS,
        self::GRADE_C,
        self::GRADE_C_MINUS,
        self::GRADE_D,
        self::GRADE_F
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    
}
