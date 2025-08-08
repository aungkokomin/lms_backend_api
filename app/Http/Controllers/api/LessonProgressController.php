<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\LessonProgressService;
use Illuminate\Http\Request;

class LessonProgressController extends Controller
{

    protected $lessonProgressService;

    public function __construct(LessonProgressService $lessonProgressService) {
        $this->lessonProgressService = $lessonProgressService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try{
            $data = $request->validate([
                'lesson_id' => 'required|integer|exists:lessons,id',
                'student_id' => 'required|integer|exists:students,id',
            ]);
            return response()->json([
                'data' => $this->lessonProgressService->createLessonProgress($data),
                'status' => 200
            ]);
        }catch(\Exception $e){
            return response()->json(['data' => $e->getMessage(),'status' => '400'],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        //
        try{
            $data = $request->validate([
                'lesson_id' => 'required|integer|exists:lessons,id',
                'student_id' => 'required|integer|exists:students,id',
            ]);

            return response()->json([
                'data' => $this->lessonProgressService->getLessonProgress($data['lesson_id'], $data['student_id']),
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json(['data' => $e->getMessage(),'status' => '400'],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        //
        try{
            $data = $request->validate([
                'lesson_id' => 'required|integer|exists:lessons,id',
                'student_id' => 'required|integer|exists:students,id',
                'is_module_complete' => 'sometimes|string|in:true,false',
                'gpa' => 'sometimes|numeric',
                'grade' => 'sometimes|string',
            ]);
            return response()->json([
                'data' => $this->lessonProgressService->updateLessonProgress($data),
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json(['data' => $e->getMessage(),'status' => '400'],400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $lesson_id, string $student_id)
    {
        //
        try {
            return response()->json([
                'data' => $this->lessonProgressService->deleteLessonProgress($lesson_id, $student_id),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function getLessonProgressByModule(Request $request)
    {
        try{
            $data = $request->validate([
                'student_id' => 'required|integer|exists:students,id',
                'module_id' => 'required|integer|exists:modules,id',
            ]);

            return response()->json([
                'data' => $this->lessonProgressService->getLessonProgressByModule($data['student_id'], $data['module_id']),
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json(['data' => $e->getMessage(),'status' => '400'],400);
        }
    }

    public function getProgressPercentageByModule(Request $request)
    {
        try{
            $data = $request->validate([
                'student_id' => 'required|integer|exists:students,id',
                'module_id' => 'sometimes|integer|exists:modules,id',
                'course_id' => 'sometimes|integer|exists:courses,id',
            ]);

            return response()->json([
                'data' => $this->lessonProgressService->getProgressPercentageByModule($data),
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json(['data' => $e->getMessage(),'status' => '400'],400);
        }
    }
}
