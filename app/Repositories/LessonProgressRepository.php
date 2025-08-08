<?php

namespace App\Repositories;

use App\Interfaces\LessonProgressRepositoryInterface;
use App\Models\LessonProgress;

class LessonProgressRepository implements LessonProgressRepositoryInterface
{
    // Add your repository methods here
    protected $lessonProgress;

    public function __construct(LessonProgress $lessonProgress) {
        $this->lessonProgress = $lessonProgress;
    }

    public function get($student_id, $lesson_id){
        return $this->lessonProgress->where('student_id',$student_id)->where('lesson_id',$lesson_id)->first();
    }

    public function create(array $data)
    {
        try{
            $lessonProgress = $this->get($data['student_id'],$data['lesson_id']);
            if(is_null($lessonProgress)){    
                $lessonProgress = $this->lessonProgress->create([
                    'lesson_id' => isset($data['lesson_id']) ? $data['lesson_id'] : null,
                    'student_id' => isset($data['student_id']) ? $data['student_id'] : null,
                    'completed_at' => $data['completed_at'] ?? null,
                    'completed' => isset($data['completed']) ? $data['completed'] : false,
                    'last_accessed_at' => now(),
                ]);
                return $lessonProgress->fresh();
            }
            
            return $lessonProgress;
        
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    public function update($lesson_id, $student_id)
    {
        $lessonProgress = $this->lessonProgress->where('lesson_id',$lesson_id)->where('student_id',$student_id)->first();
        if($lessonProgress){
            $lessonProgress->update([
                'completed' => true,
                'completed_at' => now(),
                'last_accessed_at' => now()
            ]);
        }
        return $lessonProgress;
    }

    public function delete($id)
    {
        $lessonProgress = $this->lessonProgress->findOrFail($id);
        return $lessonProgress->delete();
    }

    public function getLessonProgress($lesson_id, $student_id)
    {
        return $this->lessonProgress->where('lesson_id',$lesson_id)->where('student_id',$student_id)->first();
    }

    public function getLessonProgressByModule($student_id,$module_id)
    {
        return $this->lessonProgress->whereHas('lesson',function($query) use ($module_id){
            $query->where('module_id',$module_id);
        })->where('student_id',$student_id)->get();
    }

    public function getLessonProgressByStudent($student_id)
    {
        return $this->lessonProgress->where('student_id',$student_id)->get();
    }
}