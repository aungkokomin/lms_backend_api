<?php

namespace App\Interfaces;

interface GrantCodeRepositoryInterface
{
    // Add your Interfaces methods here
    public function getGrantInfo($grant_code);
}