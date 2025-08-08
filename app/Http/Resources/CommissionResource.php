<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommissionResource extends JsonResource
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
            'username' => $this->user ? $this->user->name : '',
            'referral_id' => $this->referral_id,
            'commission_amount' => $this->commission_amount,
            'used_own_commission_rate' => $this->used_own_commission_rate,
            'payment_amount' => $this->payment_amount,
            'applied_commission_rate' => round($this->applied_commission_rate)." %",
            'description' => $this->description,
            'created_at' => date('Y-m-d H:m:i',strtotime($this->created_at)),
            'user' => $this->user,
            // 'updated_at' => $this->updated_at,
        ];
    }
}
