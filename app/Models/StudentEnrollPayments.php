<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentEnrollPayments extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'student_enroll_payment';
    protected $fillable = ['student_id','payment_id','registration_status','payment_status'];

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }
}
