<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'NRIC_number' => $this->NRIC_number,
            'custom_student_id' => $this->custom_student_id,
            'org_name' => $this->org_name,
            'status' => 'applied',
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'applied_at' => $this->affiliate_applied_at,
            'approved_at' => $this->affiliate_approved_at,
            'rejected_at' => $this->affiliate_rejected_at,
            'id_document' => $this->document,
            'user' => $this->user ?? null,
        ];
    }
}
