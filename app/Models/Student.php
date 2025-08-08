<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Student extends Model
{
    use HasFactory,SoftDeletes,Notifiable;

    protected $fillable = ['full_name','user_id','identification_number','NRIC_number', 'nationality', 'date_of_birth', 'address', 'zip_code', 'phone_number', 'city', 'referral_id', 'last_accessed_at', 'gender','grant_applied_at','grant_approved_at','grant_rejected_at'];

    /**
     * Get the user that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id')->withTrashed();
    }

    public function enrollments(){
        return $this->hasMany(StudentEnrollPayments::class,'student_id');
    }
    
    public function education(){
        return $this->hasMany(StudentEducation::class,'student_id');
    }

    public function grants(){
        return $this->hasMany(UserGrantCode::class,'student_id');
    }

    public function document(){
        return $this->morphOne(File::class, 'fileable');
    }

    public function certifications(){
        return $this->hasMany(UserCertification::class,'student_id');
    }
}
