<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentGrantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Grant Status Logic
        if(!is_null($this->grant_applied_at) && is_null($this->grant_approved_at) && is_null($this->grant_rejected_at)){
            $grant_status = 'pending';
        }else if(!is_null($this->grant_approved_at)){
            $grant_status = 'approved';
        }else if(!is_null($this->grant_rejected_at)){
            $grant_status = 'rejected';
        }else if(is_null($this->grant_applied_at) && is_null($this->grant_approved_at) && is_null($this->grant_rejected_at)){
            $grant_status = 'not applied';
        }

        // Referrer Code and Name
        if(isset($this->user->referral[0])){
            $referrer_code = $this->user->referral[0]->referrer_id;
            if($referrer_code){
                $referrer_name = User::where('referral_id',$referrer_code)->first()->name ?? null;
            }else{
                $referrer_name = null;
            }
        }else{
            $referrer_code = null;
            $referrer_name = null;
        }

        return [
            "student_id" => $this->id,
            "identification_number" => $this->identification_number,
            "user_id" => $this->user_id,
            "NRIC_number" => $this->NRIC_number,
            "nationality" => $this->nationality,
            "date_of_birth" => $this->date_of_birth,
            "address" => $this->address,
            "zip_code" => $this->zip_code,
            "phone_number" => $this->phone_number,
            "city" => $this->city,
            "referral_id" => $this->referral_id,
            "grant_applied_at" => $this->grant_applied_at,
            "grant_approved_at" => $this->grant_approved_at,
            "grant_rejected_at" => $this->grant_rejected_at,
            "grant_status" => $grant_status,
            "email" => $this->user->email ?? null,
            "user_name" => $this->user->name ?? null,
            "student_name" => $this->full_name,
            'referrer_code' => $referrer_code,
            'referrer_name' => $referrer_name,
        ];
    }
}
