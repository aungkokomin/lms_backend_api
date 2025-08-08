<?php

namespace App\Http\Resources;

use App\Models\Bundle;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->itemable_type == 'course'){
            $result = Course::find($this->itemable_id);
            $result->is_module = false;
        }else if($this->itemable_type == 'module'){
            $result = Module::find($this->itemable_id);
            $result->is_module = true;
        }else if($this->itemable_type == 'bundle'){
            $result = Bundle::find($this->itemable_id);
            $result->is_module = false;
        }
        return [
            // 'user_id' => $this->user_id,
            'title' => $result->title, // If "is_module" is true, this will be the module title , else course title
            'description' => $result->description,
            'course_id' => $result->is_module ? $result->course_id : $result->id,
            'module_id' => $result->is_module ? $result->id : null,
            'is_module' => $result->is_module,
        ];
    }
}
