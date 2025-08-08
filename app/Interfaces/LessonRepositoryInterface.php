<?php

namespace App\Interfaces;

interface LessonRepositoryInterface
{
    public function getAll();
    
    public function getLesson($id);
    
    public function createLesson(array $data);
    
    public function updateLesson($id, array $data);

    public function deleteLesson($id);

    public function getByModule($module_id);

    public function syncLessonSorting(array $data);
}