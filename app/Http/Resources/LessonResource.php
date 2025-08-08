<?php

namespace App\Http\Resources;

use App\Models\LessonProgress;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class LessonResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();
        $student_id = $user->student()->first() ? $user->student()->first()->id : null;
        $progress = null;
        if($user->hasRole(Role::ROLE_STUDENT)){
            $progress = LessonProgress::where('lesson_id', $this->id)->where('student_id', $student_id)->first();
        }
        Log::info($progress);
        $is_completed = $progress ? $progress->completed : false;
        $is_locked = !$progress ? true : false;

        return [
            "id" => $this->id,
            "module_id" => $this->module_id,
            "sorting_order" => $this->sorting_order,
            "title" => $this->title,
            "contents" => is_null(json_decode($this->contents)) ? $this->contents : json_decode($this->contents),
            "status" => $this->status,
            "video" => $this->video,
            "quiz" => $this->quiz,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
            "deleted_at" => $this->deleted_at,
            "is_completed" => $is_completed,
            "is_locked" => $is_locked
        ];
    }
}
