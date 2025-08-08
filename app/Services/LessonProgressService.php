<?php

namespace App\Services;

use App\Interfaces\LessonProgressRepositoryInterface;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Student;

class LessonProgressService
{
    // Add your repository methods here
    protected $lessonProgressRepositoryInterface;

    public function __construct(LessonProgressRepositoryInterface $lessonProgressRepositoryInterface) {
        $this->lessonProgressRepositoryInterface = $lessonProgressRepositoryInterface;
    }

    /**
     * Summary of getLessonProgress
     * @param mixed $lesson_id 
     * @param mixed $student_id 
     * @return mixed
     */
    public function getLessonProgress($lesson_id, $student_id)
    {
        return $this->lessonProgressRepositoryInterface->get($student_id, $lesson_id);
    }

    /**
     * Summary of createLessonProgress
     * @param array $data 
     * @throws \Exception 
     * @return mixed
     */
    public function createLessonProgress(array $data)
    {
        try{
            return $this->lessonProgressRepositoryInterface->create($data);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Summary of updateLessonProgress
     * @param array $data 
     * @throws \Exception 
     * @return mixed
     */
    public function updateLessonProgress(array $data)
    {
        try{
            if(isset($data['is_module_complete'])){
                $gpa = $data['gpa'] ?? null;
                $grade = $data['grade'] ?? null;
                $this->saveCertificateRecord($data['student_id'],$data['lesson_id'],$gpa,$grade);
            }
            return $this->lessonProgressRepositoryInterface->update($data['lesson_id'], $data['student_id']);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Summary of deleteLessonProgress
     * @param mixed $lesson_id 
     * @param mixed $student_id 
     * @return mixed
     */
    public function deleteLessonProgress(string $lesson_id,string $student_id)
    {
        $lessonProgress = $this->lessonProgressRepositoryInterface->get($student_id, $lesson_id);
        return $this->lessonProgressRepositoryInterface->delete($lessonProgress->id);
    }

    /**
     * Summary of getLessonProgressByModule
     * @param mixed $student_id 
     * @param mixed $module_id 
     * @return mixed
     */
    public function getLessonProgressByModule($student_id,$module_id)
    {
        return $this->lessonProgressRepositoryInterface->getLessonProgressByModule($student_id,$module_id);
    }

    /**
     * Summary of getLessonProgressByCourse
     * @param mixed $student_id 
     * @param mixed $module_id 
     * @return mixed
     */
    public function getProgressPercentageByModule($data)
    {
        $student_id = $data['student_id'] ?? null;
        $module_id = $data['module_id'] ?? null;
        $course_id = $data['course_id'] ?? null;

        if(!$course_id && $module_id){ // Get the progress of a module
            $module = Module::find($module_id);
            if(!$module){
                throw new \Exception('Module not found');
            }
            $totalLessons = $module->lessons->count() ?? 0; // Get the total number of lessons
            $currentProgress = $this->lessonProgressRepositoryInterface
            ->getLessonProgressByModule($student_id,$module_id)
            ->where('completed',1)->count() ?? 0; // Get the total number of completed lessons
        
        }else if($course_id && !$module_id){ // Get the progress of a course
            $modules = Module::where('course_id',$course_id)->get();
            // Get the total number of lessons
            $totalLessons = $modules->map(function($module){
                return $module->lessons->count();
            })->sum();
            // Get the total number of completed lessons
            $currentProgress = $modules->map(function($module) use ($student_id){
                return $this->lessonProgressRepositoryInterface
                ->getLessonProgressByModule($student_id,$module->id)
                ->where('completed',1)
                ->count();
            })->sum();
        }else{
            throw new \Exception('Invalid parameters');
        }

        
        $result['student_id'] = $student_id;
        $result['module_id'] = $module_id;
        $result['course_id'] = $course_id;
        
        // Calculate the progress percentage
        $result['total_lessons'] = $totalLessons;
        $result['completed_lessons'] = $currentProgress;
        
        if($totalLessons == 0){
            $result['progress_percentage'] = 0;
        }else{
            $result['progress_percentage'] = round($currentProgress / $totalLessons * 100);
        }
        return $result;
    }

    public function saveCertificateRecord($student_id,$lesson_id,$gpa = null,$grade = null)
    {
        $student = Student::findOrFail($student_id);
        if(!$student){
            throw new \Exception('Student not found');
        }
        
        $module = Lesson::findOrFail($lesson_id)->module()->first();
        if(!$module){
            throw new \Exception('Module not found');
        }

        $certification = $student->certifications()->where('module_id',$module->id)->where('course_id',$module->course_id)->first();
        if(!$certification){
            $student->certifications()->create([
                'student_id' => $student->id,
                'module_id' => $module->id,
                'course_id' => $module->course_id,
                'grade' => $grade,
                'gpa_score' => $gpa,
                // 'issue_date' => now(),
                // 'expiry_date' => now()->addYears(1),
            ]);
        }
    }
}