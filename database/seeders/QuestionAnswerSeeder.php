<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\Question;
use App\Models\Quiz;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuestionAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        //
        $quizData = [
            [
                'lesson_id' => 1,
                'title' => 'Quiz 1',
                'description' => 'You need to correct answers 8 out of 10 for this quiz.',
                'passing_score' => 75,
                'time_limit' => 5
            ],
            [
                'lesson_id' => 1,
                'title' => 'Quiz 2',
                'description' => 'You need to correct answers 8 out of 10 for this quiz.',
                'passing_score' => 75,
                'time_limit' => 5
            ],
            [
                'lesson_id' => 1,
                'title' => 'Quiz 3',
                'description' => 'You need to correct answers 8 out of 10 for this quiz.',
                'passing_score' => 75,
                'time_limit' => 5
            ],
            [
                'lesson_id' => 2,
                'title' => 'Quiz 1',
                'description' => 'You need to pass Lesson 1 Quiz first.You need to correct answers 8 out of 10 for this quiz.',
                'passing_score' => 80,
                'time_limit' => 5
            ],
            [
                'lesson_id' => 2,
                'title' => 'Quiz 2',
                'description' => 'You need to pass Lesson 1 Quiz first.You need to correct answers 8 out of 10 for this quiz.',
                'passing_score' => 80,
                'time_limit' => 5
            ],
            [
                'lesson_id' => 2,
                'title' => 'Quiz 3',
                'description' => 'You need to pass Lesson 1 Quiz first.You need to correct answers 8 out of 10 for this quiz.',
                'passing_score' => 80,
                'time_limit' => 5
            ]
        ];
        foreach($quizData as $data){
            $quiz = new Quiz;
            $quiz->create($data);
        }
        $qdatas = [
            "What is your name"=>[
                ["answer_text"=>"AKKM","is_correct" => true],
                ["answer_text"=>"KKAM","is_correct" => false],
                ["answer_text"=>"KMAK","is_correct" => false]
            ],
            "How old are you"=>[
                ["answer_text" => 20, "is_correct" => false],
                ["answer_text" => 30, "is_correct" => true],
                ["answer_text" => 40, "is_correct" => false]
            ],
            "What do you do"=>[
                ["answer_text" => "Driver", "is_correct" => false],
                ["answer_text" => "Programmer", "is_correct" => false],
                ["answer_text" => "Manager", "is_correct" => true]
            ]
        ];

        try{
            $quizzes = Quiz::get();
            foreach($quizzes as $quiz){
                foreach($qdatas as $q => $ans){
                    $question = new Question;
                    $question->quiz_id = $quiz->id;
                    $question->question_text = $q;
                    $question->question_type = 'multiple_choice';
                    $question->save();
                    $answer = new Answer;
                    foreach($ans as $a){
                        $answer->create([
                            'question_id' => $question->id,
                            'answer_text' => $a['answer_text'],
                            'is_correct' => $a['is_correct']
                        ]);
                    }
                }
            }
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }    
        // $question->
    }
}
