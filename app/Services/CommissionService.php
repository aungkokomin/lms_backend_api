<?php

namespace App\Services;

use App\Interfaces\CommissionRepositoryInterface;
use App\Models\Referral;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class CommissionService
{
    // Add your repository methods here
    protected $commissionRepositoryInterface;

    public function __construct(CommissionRepositoryInterface $commissionRepositoryInterface) {
        $this->commissionRepositoryInterface = $commissionRepositoryInterface;
    }

    public function list(){
        $user = Auth::user();
        if($user){
            $referral_id = $user->referral_id;
            return $this->commissionRepositoryInterface->getByReferralId($referral_id);
        }
        throw new \Exception("Referral not found");
    }
    
}