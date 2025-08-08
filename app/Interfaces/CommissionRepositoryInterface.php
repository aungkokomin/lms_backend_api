<?php

namespace App\Interfaces;

interface CommissionRepositoryInterface
{
    // Add your Interfaces methods here
    
    public function getAll();

    public function getAllWithPaginate();

    public function getByUserId($user_id);

    public function getByReferralId($referral_id);

    public function save($data);

    public function delete($id);

    public function deleteByuserId($user_id);

    public function deleteByReferralId($referral_id);
}