<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\StudentGradeService;
use Illuminate\Http\Request;

class StudentGradeController extends Controller
{

    protected $studentGradeService;

    public function __construct(StudentGradeService $studentGradeService) {
        $this->studentGradeService = $studentGradeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            return response()->json([
                'data' => $this->studentGradeService->list(),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => [],
                'message' => $e->getMessage(),
                'status' => 400
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function getStudentGrades(Request $request){
        try{
            $data = $request->validate([
                'student_id' => 'integer|exists:students,id',
                'academic_year' => 'string',
            ]);
            return response()->json([
                'data' => $this->studentGradeService->getStudentGrades($data),
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

}
