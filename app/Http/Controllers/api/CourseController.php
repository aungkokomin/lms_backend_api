<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CourseService;
use Illuminate\Support\Facades\Validator;

class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $courses = $this->courseService->getAllCourses();
        return response()->json(['data' => $courses,'status' => 200],200);
    }

    /**
     * Display a listing of the resource. Same with index but not restricted to Authenticated users
     *
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $courses = $this->courseService->getAllCourses();
        return response()->json(['data' => $courses,'status' => 200],200);
    }

    public function show($id)
    {
        $course = $this->courseService->getCourseById($id);
        return response()->json(['data' => $course,'status' => 200],200);
    }

    public function store(Request $request)
    {
        try{
            $course = $this->courseService->createCourse($request->all());
        }catch(\Exception $e){
            return response()->json([
                "data"=> $e->getMessage(),
                "status" => 400
            ],400);
        }
        return response()->json(['data' => $course, 'status'=> 201], 201);
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->all();

            return response()->json([
                'data' => $this->courseService->updateCourse($id, $data),
                'status' => 200
            ],200);
        
        } catch (\Exception $e) {

            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function destroy($id)
    {
        
        return response()->json($this->courseService->deleteCourse($id));
    }

    public function getCourseByUserId($user_id)
    {
        return response()->json(['data' => $this->courseService->getCourseByUserId($user_id),'status' => 200],200);
    }

    public function updateCourseProgress(Request $request) 
    {
        $data = $request->all();
        try {
            //code...
            $result = $this->courseService->updateCourseProgress($data);
            return response()->json([
                'data' => $result,
                'status' => 200
            ]);
        } catch (\Exception $e) {
            //throw $e;
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ]);
        }
    }

    // public function selfEnrollCourse(Request $request)
    // {
    //     $data = $request->validate([
    //         'course_id' => 'integer|exists:courses,id',
    //         'NRIC_number' => 'required|string|max:255|unique:students,deleted_at,NULL',
    //         'nationality' => 'required|string|max:255',
    //         'date_of_birth' => 'required|date|before:today',
    //         'address' => 'required|string|max:255',
    //         'zip_code' => 'string|max:255',
    //         'email' => 'required|string|email|max:255|exists:users|unique:students,deleted_at,NULL',
    //         'phone_number' => 'required|string|max:20||unique:students,deleted_at,NULL',
    //         'city' => 'required|string|max:255',
    //         'gender' => 'required|boolean',
    //     ]);

    //     $data['role'] = 'student';

    //     $result['data'] = $this->studentService->createStudent($data);
    // }
}
