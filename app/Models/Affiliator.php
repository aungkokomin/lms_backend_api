<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Affiliator extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'affiliates';
    protected $fillable = [
        'user_id',
        'referral_id',
        'assigned_region',
        'total_recruits',
        'commission_rate',
        'performance_score',
        'monthly_target',
        'last_accessed_at',
        'affiliate_approved_at',
        'affiliate_applied_at',
        'affiliate_rejected_at',
        'full_name',
        'NRIC_number',
        'phone_number',
        'org_name',
        'country',
        'custom_student_id',
    ];

    /**
     * Get the user that owns the Student
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function student()
    {
        return $this->hasMany(Student::class,'referral_id','referral_id');
    }

    public function commission()
    {
        return $this->hasMany(Commission::class,'referral_id','referral_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function document()
    { 
        return $this->morphOne(File::class, 'fileable');
    }
}
