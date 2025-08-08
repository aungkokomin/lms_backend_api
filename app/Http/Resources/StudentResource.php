<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->grant_approved_at){
            $grant_status = 'Approved';
        }else if($this->grant_rejected_at){
            $grant_status = 'Rejected';
        }else if($this->grant_applied_at){
            $grant_status = 'Applied';
        }else if(!$this->grant_applied_at){
            $grant_status = 'Not Applied';
        }
        
        return [
            'id' => $this->id,
            'name' => $this->full_name,
            'identification_number' => $this->identification_number,
            'phone' => $this->phone,
            'NRIC_number' => $this->NRIC_number,
            'nationality' => $this->nationality,
            'date_of_birth' => $this->date_of_birth,
            'address' => $this->address,
            'zip_code' => $this->zip_code,
            'city' => $this->city,
            'referrer_code' => $this->referrer_code,
            'grant_status' => $grant_status ?? 'N/A',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'education' => $this->education,
            'user' => $this->user,
            'lesson_progress' => $this->lesson_progress,
        ];
    }
}
