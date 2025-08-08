<?php

namespace App\Interfaces;

interface QuizRepositoryInterface
{
    public function getAllQuizzes();

    public function getById($id);

    public function getByLessonId($lesson_id);
    
    public function createQuizz(array $data);
    
    public function updateQuizz($id, array $data);
    
    public function deleteQuizz($id);
    
    public function getRandomQuestions($id);
    
    public function submitAnswers(array $data,array $result);

    public function checkQuizProgress($quiz_id,$student_id);

    public function getLastAttemptResult($quiz_id,$student_id);
}