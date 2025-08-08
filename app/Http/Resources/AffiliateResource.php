<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'affiliate_id' => $this->id,
            'full_name' => $this->full_name,
            'identification_no' => $this->identification_no,
            'custom_student_id' => $this->custom_student_id,
            'phone_number' => $this->phone_number,
            'org_name' => $this->org_name,
            'country' => $this->country,
            'assigned_region' => $this->assigned_region,
            'commission_rate' => $this->commission_rate,
            'monthly_target' => $this->monthly_target,
            'total_recruits' => $this->total_recruits,
            'performance_score' => $this->performance_score,
            'last_accessed_at' => $this->last_accessed_at,
            'affiliate_approved_at' => $this->affiliate_approved_at,
            'id_document' => $this->document ?? null,
            'user' => $this->user ?? null,
            'student' => $this->student ?? null,
            'commission' => $this->commission ?? null,
        ];
    }
}
