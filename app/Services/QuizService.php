<?php

namespace App\Services;
use App\Interfaces\QuizRepositoryInterface;
use App\Models\Answer;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizReattemptAppeal;
use App\Models\Student;
use App\Models\StudentGrading;
use App\Models\StudentQuiz;
use App\Models\UserCertification;
use Dflydev\DotAccessData\Exception\DataException;
use Dotenv\Exception\ValidationException;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;




class QuizService
{
    protected $quizRepoInterface;

    public function __construct(QuizRepositoryInterface $quizRepoInterface) {
        $this->quizRepoInterface = $quizRepoInterface;
    }

    /**
     * Summary of getAllQuizzes
     * @return mixed
     */
    public function getAllQuizzes()
    {
        // Add your code here
        return $this->quizRepoInterface->getAllQuizzes();
    }

    /**
     * Summary of getByLessonId
     * @param mixed $lesson_id
     * @return mixed
     */
    public function getByLessonId($lesson_id)
    {
        // get Lesson by id
        return $this->quizRepoInterface->getByLessonId($lesson_id);
    }

    /**
     * Summary of createQuizz
     * @param array $data
     * @throws \Dotenv\Exception\ValidationException
     * @throws \Exception
     * @return mixed
     */
    public function createQuizz(array $data){

        try{
            $validated = Validator::make($data,[
                'lesson_id' => 'required',
                'title' => 'required|string|max:255',
                'description' => 'sometimes|string|nullable',
                'passing_score' => 'numeric|max:100',
                'time_limit' => 'numeric',
            ]);
            if($validated->fails()){
                throw new ValidationException($validated->errors());
            }

            // Create the quiz and sync the questions and answers
            $quiz = $this->quizRepoInterface->createQuizz($validated->validated());
            if(isset($data['questions'])){
                $this->syncQuestionAnswerDatas(json_decode($data['questions']),$quiz);
            }
            
            return $quiz;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Summary of updateQuizz
     * @param mixed $id
     * @param array $data
     * @throws \Dotenv\Exception\ValidationException
     * @throws \Exception
     * @return mixed
     */
    public function updateQuizz($id, array $data){
        try{
            $validated = Validator::make($data,[
                'lesson_id' => 'required',
                'title' => 'required|string|max:255',
                'description' => 'sometimes|string|nullable',
                'passing_score' => 'numeric|max:100',
                'time_limit' => 'numeric',
            ]);

            if($validated->fails()){
                throw new ValidationException($validated->errors());
            }

            // Update the quiz and sync the questions and answers
            $quiz = $this->quizRepoInterface->updateQuizz($id,$validated->validated());
            
            // Delete the questions and answers
            if($quiz){
                $questions = $quiz->questions()->get();
                if($questions->count()){
                    foreach($questions as $question){
                        $question->answers()->delete();
                    }
                    $quiz->questions()->delete();    
                }
            }
            
            if(isset($data['questions'])){
                $this->syncQuestionAnswerDatas(json_decode($data['questions']),$quiz);
            }
            
            return $quiz;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Summary of get
     * @param mixed $id
     * @return mixed
     */
    public function get($id)  {
        return $this->quizRepoInterface->getById($id);
    }


    /**
     * Summary of deleteQuizz
     * @param mixed $id
     * @return void
     */
    public function deleteQuizz($id){
        return $this->quizRepoInterface->deleteQuizz($id);
    }

    /**
     * Summary of checkQuizProgress
     * @param mixed $quiz_id
     * @return void
     */
    public function checkQuizProgress($quiz_id)
    {
        $student_id = Auth::user()->student()->firstOrFail()->id;
        return $this->quizRepoInterface->checkQuizProgress($quiz_id,$student_id);
    }

    /**
     * Summary of getLastAttemptResult
     * @param mixed $quiz_id
     * @return mixed
     */
    public function getLastAttemptResult($quiz_id){
        $student_id = Auth::user()->student()->firstOrFail()->id;
        $result = $this->quizRepoInterface->getLastAttemptResult($quiz_id,$student_id);
        return $result ? $result : 'not-attempted';
    }

    /**
     * Summary of getRandomQuestions
     * @param mixed $lesson_id
     * @return mixed
     */
    public function getRandomQuestions($quiz){
        $questions =  $quiz->questions()->with(['answers' => function($a) {
            $a->select('id','question_id','answer_text')->inRandomOrder();
        }])->inRandomOrder()->limit(10)->get();
        
        $questions->map(function($q,$index){
            $q->question_number = $index + 1;
            $index++;
        });

        return $questions;
    }

    /**
     * Summary of submitAnswers
     * @param array $data
     * @throws \Dotenv\Exception\ValidationException
     * @throws \Dflydev\DotAccessData\Exception\DataException
     * @return array
     */
    public function submitAnswers(array $data){

        $data['answers'] = $answers = json_decode($data['answers']); // Decode the answers

        // Check if the answers is not a valid JSON
        if(json_last_error() !== JSON_ERROR_NONE){
            throw new DataException('Invalid JSON format');
        }

        // Validate the data
        $validator = Validator::make($data,[
            'quiz_id' => 'required|exists:quizzes,id',
        ]);

        // Check if the validation fails
        if($validator->fails()){
            throw new ValidationException($validator->errors()->first());
        }

        // Check if the quiz already attempted
        $quiz_already_completed = $this->checkQuizProgress($data['quiz_id']) ? true : false;
        if($quiz_already_completed){
            throw new DataException('Quiz already attempted');
        }
        
        // Mark the quiz answers
        $result = $this->markingQuizAnswers($answers,$data['total_questions']);
        Log::info('Quiz marked successfully');

        // Get the quiz
        $quiz = $this->quizRepoInterface->getById($data['quiz_id']);

        // Check if the student passed or failed the quiz
        if($result['score_percentage'] < $quiz->passing_score){
            $quiz_status = 'failed';
        }else{
            $quiz_status = 'passed';
        }

        // Submit the answers to the database
        $submitAns =  $this->quizRepoInterface->submitAnswers($data,[
            'score' => $result['score'],
            'qIDs' => $result['qIDs'],
            'status' => $quiz_status,
        ]);
        Log::info('Quiz submitted successfully');

        // Grade the student
        $grade_result = $this->studentGradingProcess($result,$quiz,$data['student_id']);
        Log::info('Student graded successfully');

        $quiz_already_completed = $this->checkQuizProgress($data['quiz_id']) ? true : false;
        Log::info('Quiz Progress Complete Status Updated');

        return [
            'answers' => $result['data'],
            'score' => $submitAns['score'],
            'score_percentage' => $result['score_percentage'],
            'status' =>  $submitAns['status'],
            'total_questions' => $data['total_questions'],
            'correct_numbers' => $result['correct_q_number'],
            'wrong_numbers' => $result['wrong_q_number'],
            'passing_score' => $quiz->passing_score,
            'grade' => $grade_result['grade'],
            'gpa' => $grade_result['gpa'],
            'is_completed' => $quiz_already_completed,
        ];
    }

    /**
     * Summary of gradingQuizAttempts
     * @param array $datas
     * @return array
     */
    public function markingQuizAnswers(array $datas,int $total_count){

        // Initialize the result
        $score = 0;
        $questions = [];
        $correct_q_number = [];
        $wrong_q_number = [];

        $user_id = Auth::user()->id;
        // Loop through the answers
        foreach($datas as $data){
            // Check if the answer is null
            if($data->answer_id == null){
                $wrong_q_number[] = $data->question_number;
                $data->is_correct = 0;
            }else{

                $data->is_correct = Answer::findOrFail($data->answer_id)->is_correct; // Check if the answer is correct
                if($data->is_correct){
                    $score += 1;
                    $correct_q_number[] = $data->question_number;
                }else{
                    $wrong_q_number[] = $data->question_number;
                }
            }

            $result['data'][] = (array) $data;
            array_push($questions,$data->question_id); // Push the question id to the questions array
        }

        // Calculate the result
        $result['wrong_q_number'] = count($wrong_q_number) ? $wrong_q_number : NULL;
        $result['correct_q_number'] = count($correct_q_number) ? $correct_q_number : NULL;
        $result['score_percentage'] = round($score/$total_count * 100);
        $result['score'] = $score;
        $result['qIDs'] = $questions;

        return $result;
    }

    /**
     * Summary of syncQuestionAnswerDatas
     * @param array $questions
     * @param object $quiz
     * @return void
     */
    private function syncQuestionAnswerDatas(array $questions,object $quiz){
        foreach($questions as $q){
            $question = $quiz->questions()->create([
                'question_text' => $q->question,
                'question_type' => $q->question_type,
            ]);
            foreach($q->answers as $ans){
                $question->answers()->create([
                    'answer_text' =>  $ans->answer_text,
                    'is_correct' => $ans->is_correct,
                ]);
            }
        }
    }

    /**
     * Summary of studentGradingProcess
     * @param array $result
     * @return array
     */
    public function studentGradingProcess(array $result,$quiz,$student_id){

        $total_score = 0;   // Total score of the student
        $total_questions = 0; // Total questions attempted by the student
        
        $lesson = $quiz->lesson()->first(); // Get the lesson
        $quizees_in_module = $quiz->whereIn('lesson_id',$lesson->module->lessons()->pluck('id'))->get(); // Get all quizes in the module
        
        // Get all quiz in the module attempts by student
        $quiz_attempts = StudentQuiz::where('student_id',$student_id)
        ->whereIn('quiz_id',$quizees_in_module->pluck('id'))
        ->where('status','passed')
        ->get();
        
        // Calculate the total score and total questions attempted by the student
        foreach($quiz_attempts as $quiz){
            $attempted_questions = json_decode($quiz->attempted_questions);
            $total_score += $quiz->score;
            $total_questions += count($attempted_questions);
        }
        
        // Calculate the total score percentage
        if($total_questions == 0){
            $total_score_percentage = 0;
        }else{
            $total_score_percentage = round($total_score/$total_questions * 100);
        }

        // Calculate the GPA and Grade
        if(isset($total_score_percentage)){

            // Initialize the GPA and Grade
            $gpa = null;
            $grades = null;

            // Calculate the GPA
            $gpa = $total_score_percentage * 0.04;

            // Calculate the Grade
            switch($gpa){
                case $gpa < 1.00:
                    $grades = StudentGrading::GRADE_F;
                    break;
                case $gpa >= 1.00 && $gpa < 1.67:
                    $grades = StudentGrading::GRADE_D;
                    break;
                case $gpa >= 1.67 && $gpa < 2.00:
                    $grades = StudentGrading::GRADE_C_MINUS;
                    break;
                case $gpa >= 2.00 && $gpa < 2.33:
                    $grades = StudentGrading::GRADE_C;
                    break;
                case $gpa >= 2.33 && $gpa < 2.67:
                    $grades = StudentGrading::GRADE_C_PLUS;
                    break;
                case $gpa >= 2.67 && $gpa < 3.00:
                    $grades = StudentGrading::GRADE_B_MINUS;
                    break;
                case $gpa >= 3.00 && $gpa < 3.33:
                    $grades = StudentGrading::GRADE_B;
                    break;
                case $gpa >= 3.33 && $gpa < 3.67:
                    $grades = StudentGrading::GRADE_B_PLUS;
                    break;
                case $gpa >= 3.67 && $gpa < 4.00:
                    $grades = StudentGrading::GRADE_A_MINUS;
                    break;
                case $gpa >= 4.00:
                    $grades = StudentGrading::GRADE_A;
                    break;
                default:
                    $grades = null;
            }
        }

        // Update the student grading
        $module_id = $lesson->module_id;
        $gradings = StudentGrading::where('student_id',$student_id)->where('module_id',$module_id)->get();

        if($gradings->count()){ // Check if the student grading exists
            foreach($gradings as $grading){ // Update the student grading
                $grading->update([
                    'grade' => $grades,
                    'gpa_score' => $gpa,
                ]);
            }
        }else{
            $gradings = StudentGrading::create([ // Create a new student grading
                'student_id' => $student_id,
                'module_id' => $module_id,
                'grade' => $grades,
                'gpa_score' => $gpa,
            ]);
        }

        return [
            'grade' => $grades,
            'gpa' => $gpa,
        ];
    }

    public function checkQuizReattemptAppeal($lesson_id){
        $student_id = Auth::user()->student()->firstOrFail()->id;
        $reattemptAppeal = QuizReattemptAppeal::whereIn('quiz_id',DB::table('quizzes')->select('id')->where('lesson_id',$lesson_id))
        ->where('student_id',$student_id)
        ->where('status',QuizReattemptAppeal::STATUS_PENDING)
        ->count();
        return $reattemptAppeal;
    }

}