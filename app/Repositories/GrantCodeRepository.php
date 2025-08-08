<?php

namespace App\Repositories;

use App\Interfaces\GrantCodeRepositoryInterface;
use App\Models\UserGrantCode;

class GrantCodeRepository implements GrantCodeRepositoryInterface
{
    // Add your repository methods here
    protected $userGrantCode;

    public function __construct(UserGrantCode $userGrantCode) {
        $this->userGrantCode = $userGrantCode;
    }

    public function getGrantInfo($grant_code)
    {
        return $this->userGrantCode->where('code', $grant_code)->first();
    }
}