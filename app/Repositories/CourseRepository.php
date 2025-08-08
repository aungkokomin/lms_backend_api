<?php

namespace App\Repositories;

use App\Interfaces\CourseRepositoryInterface;
use App\Models\Course;
use App\Models\CourseProgress;
use Illuminate\Support\Facades\DB;

class CourseRepository implements CourseRepositoryInterface
{
    protected $course;

    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function getAllCourses()
    {
        return $this->course->with('image')->orderBy('created_at')->paginate(100);
    }

    public function getCourseById($id)
    {
        return $this->course->with('image')->findOrFail($id);
    }

    public function createCourse(array $data)
    {
        return $this->course->create([
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'status' => $data['status']
        ]);
    }

    public function updateCourse($id, array $data)
    {
        $course = $this->course->findOrFail($id);
        $course->update($data);
        return $course->fresh();
    }

    public function deleteCourse($id)
    {
        $course = $this->course->findOrFail($id);
        $course->bundles()->detach();
        $course->image()->delete();
        return $course->delete();
    }

    public function getCourseByUserId($user_id)
    {
        return CourseProgress::where('user_id',$user_id)->get();
    }

    public function saveCourseProgress($data)
    {
        $progress = CourseProgress::where('user_id',$data['user_id'])
        ->where('course_id',$data['course_id'])
        ->first();

        if($progress){
            $data['completed_modules'] = $progress->completed_modules > $data['completed_modules'] ? $progress->completed_modules : $data['completed_modules'];
            return $progress->update($data);
        }else{
            return $progress->create($data);
        }
    }
}
