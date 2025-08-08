<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuizReattemptAppealResource;
use App\Services\QuizReattemptAppealService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuizReattemptAppealController extends Controller
{
    protected $quizReattemptAppealService;

    public function __construct(QuizReattemptAppealService $quizReattemptAppealService){
        $this->quizReattemptAppealService = $quizReattemptAppealService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try{
            $result['status'] = 200;
            $data = $request->validate([
                'searchValue' => 'sometimes|string|nullable',
                'start_date' => 'sometimes|date|nullable',
                'end_date' => 'sometimes|date|nullable',
                'status' => 'sometimes|in:pending,approved,rejected'
            ]);
            
            // Assigning the values to the variables
            $searchValue = $data['searchValue'] ?? null;
            $start_date = isset($data['start_date']) ? date('Y-m-d',strtotime($data['start_date'])) : null;
            $end_date = isset($data['end_date']) ? date('Y-m-d',strtotime($data['end_date'])) : null;
            $status = $data['status'] ?? null;


            $appleals = $this->quizReattemptAppealService->getPaginate($searchValue,$start_date,$end_date,$status);
            $result['data'] = QuizReattemptAppealResource::collection($appleals);
            return response()->json([
                'data' => $result,
                'current_page' => $appleals->currentPage(),
                'total' => $appleals->total(),
                'per_page' => $appleals->perPage(),
                'last_page' => $appleals->lastPage(),
            ],$result['status']);
        }catch(\Exception $e){
            return response()->json([
                'status' => 400,
                'data' => $e->getMessage()
            ],400);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $data = $request->validate([
                'quiz_id' => 'required|exists:quizzes,id,deleted_at,NULL',
                'student_id' => 'required|exists:students,id,deleted_at,NULL',
            ]);
            $result = $this->quizReattemptAppealService->create($data);
            return response()->json([
                'data' => $result,
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
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

    public function approveRequest(Request $request){
        try{
            $data = $request->validate([
                'id' => 'required|exists:quiz_reattempt_appeals,id',
                'status' => 'required|in:approved,rejected'
            ]);

            $result = $this->quizReattemptAppealService->reattemptAppealDecision($data['id'],$data['status']);
            return response()->json([
                'data' => $result,
                'status' => 200
            ],200);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function getReattemptedCountOverList()
    {
        try{
            $result = $this->quizReattemptAppealService->getReattemptedCountOverList();
            return response()->json([
                'data' => $result,
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }
}
