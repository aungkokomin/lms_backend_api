<?php

namespace App\Http\Resources;

use App\Models\Bundle;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $order_items = json_decode($this->order_items);
        if(isset($order_items)){
            foreach($order_items as $order_item){
                if($order_item->itemable_type == "course"){
                    $items[$order_item->itemable_type][] = Course::find($order_item->itemable_id);
                }else if($order_item->itemable_type == "module"){
                    $items[$order_item->itemable_type][] = Module::find($order_item->itemable_id);
                }else if($order_item->itemable_type == "bundle"){
                    $items[$order_item->itemable_type][] = Bundle::find($order_item->itemable_id);
                }
            }
        }
        return [
            'id' => $this->id,
            'order_uid' => $this->order_uid,
            // 'user_id' => $this->user_id,
            'user' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ] : [
                'id' => null,
                'name' => null,
                'email' => null,
            ],
            'status' => $this->status,
            'transaction_id' => $this->transaction_id,
            'order_price' => $this->order_price,    
            'order_items' => isset($items) ? $items : [],
            'payment_method' => $this->payment_method,
            'order_type' => $this->order_type,
            'order_date' => $this->order_date,
            'completed_at' => $this->completed_at,
            'payment' => $this->payment,
        ];
    }
}
