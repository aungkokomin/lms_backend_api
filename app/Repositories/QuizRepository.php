<?php

namespace App\Repositories;
use App\Interfaces\QuizRepositoryInterface;
use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\StudentQuiz;
use Dflydev\DotAccessData\Exception\DataException;

class QuizRepository implements QuizRepositoryInterface
{
    protected $quiz;

    public function __construct(Quiz $quiz) {
        $this->quiz = $quiz;
    }

    /**
     * Get all quizzes
     * @return mixed
     */
    // Add your repository methods here
    public function getAllQuizzes(){
        return $this->quiz->get();
    }

    /**
     * Get a quiz by id
     * @param $id
     * @return mixed
     */
    public function getById($id){
        return $this->quiz->with('questions.answers')->findOrFail($id);
    }

    public function getByLessonId($lesson_id){
        // $quiz = $this->quiz->with('questions')->where( 'lesson_id',$lesson_id)->get();
        // $quiz->map(function($quiz){
        //     foreach($quiz->questions as $question){
        //         $question->answers = Answer::where('question_id',$question->id)->get();
        //     }
        // });
        return $this->quiz->with('questions.answers')->where( 'lesson_id',$lesson_id)->orderBy('id','desc')->firstOrFail();
    }

    /**
     * Create a quiz
     * @param array $data
     * @return mixed
     * @throws DataException
     */
    public function createQuizz(array $data){
        if($this->quiz->where('lesson_id',$data['lesson_id'])->count()){
            throw new DataException("Quiz already exists for this lesson");
        }
        return $this->quiz->create($data);
    }

    /**
     * Update a quiz
     * @param $id
     * @param array $data
     * @return mixed
     * @throws DataException
     */
    public function updateQuizz($id, array $data){
        $quiz = $this->getById($id);
        if($quiz->update($data)){
            return $quiz->fresh();
        }else{
            throw new DataException("Update Failed");
        }
    }

    /**
     * Delete a quiz
     * @param $id
     * @return mixed
     */
    public function deleteQuizz($id){
        $quiz = $this->quiz->findOrFail($id);
        return $quiz->delete();
    }

    public function getRandomQuestions($id){

    }

    /**
     * Submit answers for a quiz
     * @param array $data
     * @param array $result
     * @return mixed
     * @throws DataException
     */
    public function submitAnswers(array $data,array $result){
        $quizAttempt = new StudentQuiz();
        
        try{
            return $quizAttempt->create([
                'student_id' => $data['student_id'],
                'quiz_id' => $data['quiz_id'],
                'score' => $result['score'],
                'attempted_questions' => json_encode($result['qIDs']),
                'status' => $result['status'],
                'attempt_date' => date('Y-m-d')
            ]);
        }catch(\Exception $e){
            throw new DataException($e->getMessage());
        }
    }

    /**
     * Check quiz progress
     * @param $quiz_id
     * @return bool
     * @throws DataException
     */
    public function checkQuizProgress($quiz_id,$student_id){
        return StudentQuiz::where('quiz_id',$quiz_id)->where('student_id',$student_id)->where('status','passed')->count();
    }

    /**
     * Get the last attempt result
     * @param $quiz_id
     * @param $student_id
     * @return mixed
     */
    public function getLastAttemptResult($quiz_id,$student_id){
        return StudentQuiz::where('quiz_id',$quiz_id)->where('student_id',$student_id)->orderBy('id','desc')->first();
    }


}