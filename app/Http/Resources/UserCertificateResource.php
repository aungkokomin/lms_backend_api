<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCertificateResource extends JsonResource
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
            'student_id' => $this->student_id,
            'student' => $this->student,
            'course_title' => $this->course ? $this->course->title : null,
            'module_title' => $this->module ? $this->module->title : null,
            'issue_date' => $this->issue_date,
            'expiry_date' => $this->expiry_date,
            'status' => $this->status,
            'gpa_score' => $this->gpa_score,
            'grade' => $this->grade,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'certificate_file_name' => $this->certificate_file,
            'certificate_file' => $this->certificateFile ? $this->certificateFile->url : "/certification-sample.jpg", //Replace with actual file path and url generation logic
        ];
    }
}
