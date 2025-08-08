<?php

namespace App\Services;

use App\Interfaces\StudentGradingRepositoryInterface;

class StudentGradeService
{
    // Add your repository methods here
    protected $studentGradingRepositoryInterface;

    public function __construct(StudentGradingRepositoryInterface $studentGradingRepositoryInterface) {
        $this->studentGradingRepositoryInterface = $studentGradingRepositoryInterface;
    }

    /**
     * Get all student grades
     * 
     * @return mixed
     */
    public function list(){
        return $this->studentGradingRepositoryInterface->list();
    }

    public function getStudentGrades($data){
        return $this->studentGradingRepositoryInterface->getStudentGrades($data);
    }
}