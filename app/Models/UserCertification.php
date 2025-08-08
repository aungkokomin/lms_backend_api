<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCertification extends Model
{
    use HasFactory;
    
    protected $table = 'user_certifications';

    protected $fillable = ['student_id','course_id','module_id','issue_date','expiry_date','certificate_file','status','gpa_score','grade','notes'];


    const STATUS_ISSUED = 'Issued';
    const STATUS_PENDING = 'Pending';
    const STATUS_EXPIRED = 'Expired';
    const STATUS_REVOKED = 'Revoked';
    
    const STATUS = [
        self::STATUS_ISSUED,
        self::STATUS_PENDING,
        self::STATUS_EXPIRED,
        self::STATUS_REVOKED
    ];

    // const EXPIRE_DATE = now()->addYears(1);
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id');
    }

    public function certificateFile()
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
