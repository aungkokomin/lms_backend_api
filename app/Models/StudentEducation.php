<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentEducation extends Model
{
    use HasFactory;

    protected $table = 'student_educations';
    protected $fillable = ['student_id','field_of_study','degree','academic_year'];

    public function student(){
        return $this->belongsTo(Student::class,'student_id');
    }

    public function document(){
        return $this->morphOne(File::class,'fileable');
    }
}
