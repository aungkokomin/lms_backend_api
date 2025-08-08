<?php

namespace App\Interfaces;

interface LessonProgressRepositoryInterface
{
    // Add your Interfaces methods here
    public function get($student_id, $lesson_id);

    public function create(array $data);

    public function update($lesson_id, $student_id);

    public function delete($id);

    public function getLessonProgress($lesson_id, $student_id);

    public function getLessonProgressByModule($student_id,$module_id);
}