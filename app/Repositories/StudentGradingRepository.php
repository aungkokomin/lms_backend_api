<?php

namespace App\Repositories;

use App\Interfaces\StudentGradingRepositoryInterface;
use App\Models\StudentGrading;
use Exception;

class StudentGradingRepository implements StudentGradingRepositoryInterface
{
    // Add your repository methods here

    protected $studentGrading;

    public function __construct(StudentGrading $studentGrading) {
        $this->studentGrading = $studentGrading;
    }

    public function list(){
        return $this->studentGrading->with('student','module')->paginate(50);
    }

    public function getById($id){
        return $this->studentGrading->with('student','module')->first($id);
    }

    public function create(array $data){
        return $this->studentGrading->create($data);
    }

    public function update($id, array $data){
        $studentGrading = $this->getById($id);
        if($studentGrading->update($data)){
            return $studentGrading->fresh();
        }else{
            throw new Exception("Update Failed");
        }
    }

    public function delete($id){
        $studentGrading = $this->studentGrading->findOrFail($id);
        return $studentGrading->delete();
    }

    public function getStudentGrades($data){
        return $this->studentGrading->with('module')->where('student_id',$data['student_id'])->get();
    }

    public function getStudentGradeByModuleId($module_id){
        return $this->studentGrading->with('module')->where('module_id',$module_id)->get();
    }

}