<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;

class Role extends Model
{
    use HasFactory, HasRoles;

    protected $fillable = ["name"];

    protected $guard_name = ["sanctum"];

    const ROLE_ADMIN = "admin";
    const ROLE_STUDENT = "student";
    const ROLE_AGENT = "affiliate";
    const ROLE_GUEST = "guest";

    public static function getRoles()
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_STUDENT,
            self::ROLE_AGENT,
            self::ROLE_GUEST,
        ];
    }
}
