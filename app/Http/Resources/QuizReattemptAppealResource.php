<?php

namespace App\Http\Resources;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizReattemptAppealResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->student){
            $user = User::withTrashed()->find($this->student->user_id);
        }
        if($this->quiz){
            $quiz = Quiz::with('lesson')->find($this->quiz_id);
        }
        return [
            "id" => $this->id,
            "quiz_id" => $this->quiz_id,
            "lesson_title" => $quiz->lesson->title ?? null,
            "student_id" => $this->student_id,
            "identification_number" => $this->student ? $this->student->identification_number : null,
            "user_name" => $user->name ?? null,
            "student_name" => $this->student->full_name ?? null,
            "status" =>  $this->status,
            "applied_at" =>  $this->created_at,
            "approved_at" =>  $this->approved_at,
            "rejected_at" => $this->rejected_at,
            // "student" => $this->student,
            // "quiz" => $this->quiz
        ];
    }
}
