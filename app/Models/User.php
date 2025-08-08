<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\CustomResetPassword;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $guard_name ='sanctum';

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomResetPassword($token));
    }

    /**
     * Get all of the students for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function student(): HasMany
    {
        return $this->hasMany(Student::class,'user_id');
    }

    public function instructor(): HasMany
    {
        return $this->hasMany(Instructor::class,'user_id');
    }

    public function affiliator(): HasMany
    {
        return $this->hasMany(Affiliator::class,'user_id');
    }

    public function referral(): HasMany
    {
        return $this->hasMany(Referral::class,'user_id');
    }

    public function order(): HasMany
    {
        return $this->hasMany(Order::class,'user_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class,'imageable');
    }

    public function userItem()
    {
        return $this->hasMany(UserItem::class,'user_id');
    }

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}
