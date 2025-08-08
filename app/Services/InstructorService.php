<?php

namespace App\Services;

use App\Interfaces\InstructorRepositoryInterface;

class InstructorService
{
    // Add your repository methods here
    
    protected $instructorRepositoryInterface;
    
    public function __construct(InstructorRepositoryInterface $instructorRepositoryInterface) {
        $this->instructorRepositoryInterface = $instructorRepositoryInterface;
    }
    /**
     * Summary of studentList
     * @return mixed
     */
    public function listInstructor(){
        return $this->instructorRepositoryInterface->list();
    }

    public function createInstructor($data)
    {

        return $this->instructorRepositoryInterface->create($data);
        
    }

    public function showInstructor($id)
    {
        return $this->instructorRepositoryInterface->show($id);    
    }
}