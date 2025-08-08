<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserLoginResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $student = $this->student()->first();
        return [
            "id"=> $this->id,
            "student_id" => $student ? $student->id : null,
            "identification_number" => $student ? $student->identification_number : null,
            "name"=>  $this->name,
            "full_name"=>  $student ? $student->full_name : null,
            "NRIC_number"=>  $this->NRIC_number,
            "nationality"=>  $this->nationality,
            "date_of_birth"=> $this->date_of_birth,
            "address"=> $this->address,
            "zip_code"=>  $this->zip_code,
            "email"=> $this->email,
            "phone_number"=> $this->phone_number,
            "city"=>  $this->city,
            "referral_id"=>  $this->referral_id,
            "email_verified_at"=>  $this->email_verified_at,
            "last_accessed_at"=>  $this->last_accessed_at,
            "gender"=>  $this->gender,
            "created_at"=>  $this->created_at,
            "updated_at"=>  $this->updated_at,
            "deleted_at"=>  $this->deleted_at,
            "image"=>  $this->image,
            "roles"=>  $this->roles,
        ];
    }
}
