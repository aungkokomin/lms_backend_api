<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\Module;
use App\Models\Student;
use App\Models\StudentGrading;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class SyncDummyAccountUnlockAllCourse extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $user = User::where('email', 'azm1961@gmail.com')->first();
        $student = Student::where('user_id',$user->id)->first();
        $course = Course::find(1);
        $modules = Module::where('course_id', $course->id)->get();
        // $modules = Module::whereIn('course_id', [10,11,12,13,14])->get();
        foreach ($modules as $module) {
            $lessons = Lesson::where('module_id', $module->id)->get();
            foreach ($lessons as $lesson) {
                
                $progress = LessonProgress::where('student_id', $student->id)
                    ->where('lesson_id', $lesson->id)
                    ->first();
                if ($progress){
                    continue;
                }
                LessonProgress::create([
                    'student_id' => $student->id,
                    'lesson_id' => $lesson->id,
                    'completed' => 1,
                    'last_accessed_at' => now(),
                    'completed_at' => now(),
                ]);
                // $progress->fill($data);
                Log::info('student - '.$student->id.'/ lesson - '.$lesson->id);
            }

            $gradings = StudentGrading::where('student_id',$student->id)->where('module_id',$module->id)->get();

            if($gradings->count()){ // Check if the student grading exists
                foreach($gradings as $grading){ // Update the student grading
                    $grading->update([
                        'grade' => NULL,
                        'gpa_score' => NULL,
                    ]);
                }
            }else{
                $gradings = StudentGrading::create([ // Create a new student grading
                    'student_id' => $student->id,
                    'module_id' => $module->id,
                    'grade' => NULL,
                    'gpa_score' => NULL,
                ]);
            }
        }
        // dd($data);
    }
}
