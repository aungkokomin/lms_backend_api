<?php

namespace App\Services;

use App\Interfaces\GrantCodeRepositoryInterface;
use App\Models\User;
use App\Models\UserGrantCode;

class GrantCodeService
{
    // Add your repository methods here
    protected $grantCodeRepositoryInterface;

    public function __construct(GrantCodeRepositoryInterface $grantCodeRepositoryInterface) {
        $this->grantCodeRepositoryInterface = $grantCodeRepositoryInterface;
    }

    public function getGrantInfo($data)
    {
        $grant_code = $data['grant_code'];
        $grantInfo =  $this->grantCodeRepositoryInterface->getGrantInfo($grant_code);

        $message = UserGrantCode::STATUS_ACTIVE;
        if($grantInfo == null){
            $message = UserGrantCode::STATUS_INACTIVE;
        }else if($grantInfo->used_at != null){
            $message = UserGrantCode::STATUS_USED;
        }else if($grantInfo->expired_at < now()){
            $message = UserGrantCode::STATUS_EXPIRED;
        } else if(!$grantInfo->is_active){
            $message = UserGrantCode::STATUS_INACTIVE;
        }

        return [
            'data' => $grantInfo,
            'message' => $message
        ];
    }
}