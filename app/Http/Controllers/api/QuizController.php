<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\QuestionResource;
use App\Services\QuizService;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    protected $quizService;

    public function __construct(QuizService $quizzService) {
        $this->quizService = $quizzService;
    }
    /**
     * Display a listing of the resource.
     */
    public function listByLesson($lesson_id)
    {
        //
        try {
            return response()->json([
                'data' => $this->quizService->getByLessonId($lesson_id),
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
        try{
            $data = $request->all();
            return response()->json([
                'data' => $this->quizService->createQuizz($data),
                'status' => 201
            ],201);
        }catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        try {

            return response()->json(['data'=>$this->quizService->get($id),'status' => 200],200);
        } catch (\Exception $e) {

            return response()->json(['data' => $e->getMessage(),'status' => 400],400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
        try {
            return response()->json([
                'data' => $this->quizService->updateQuizz($id,$request->all()),
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            return response()->json([
                'data' => $this->quizService->deleteQuizz($id),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function startQuiz(Request $request)
    {
        try {
            $data = $request->validate([
                'lesson_id' => 'required|integer|exists:lessons,id',
            ]);
            $appealFlag = $this->quizService->checkQuizReattemptAppeal($data['lesson_id']); // Check if there is an appeal for reattempt
            $quiz = $this->quizService->getByLessonId($data['lesson_id']); // Get the quiz for the lesson
            $is_completed = $this->quizService->checkQuizProgress($quiz->id) ? true : false; // Check if the quiz is completed
            $last_attempt_result = $this->quizService->getLastAttemptResult($quiz->id); // Get the last attempt result

            if($appealFlag) { // If there is an appeal for reattempt
                $data = [];
            }else{ // If there is no appeal for reattempt
                $questions = $this->quizService->getRandomQuestions($quiz);
                $data = QuestionResource::collection($questions);
            }
            
            return response()->json([
                'data' => $data,
                'quiz' => $quiz,
                'reattempt_appeal_flag' => $appealFlag ? true : false,
                'is_completed' => $is_completed,
                'last_attempt_result' => $last_attempt_result,
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 404
            ]);
        }
        // Retrieve 10 random questions for the quiz using the service
    }

    public function submitAnswers(Request $request)
    {  
        try{
            $data = $request->validate([
                'student_id' => 'required|integer|exists:students,id',
                'quiz_id' => 'required|integer|exists:quizzes,id',
                'answers' => 'required',
                'total_questions' => 'required|integer',
            ]);
            $result = $this->quizService->submitAnswers($request->all());
            return response()->json([
                'data' => $result,
                'status' => 200,
            ],200);
        } catch(\Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }
}
