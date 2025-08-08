<?php

namespace App\Interfaces;

interface StudentGradingRepositoryInterface
{
    // Add your Interfaces methods here
    public function list();
    public function getById($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getStudentGrades($data);
    public function getStudentGradeByModuleId($module_id);
}