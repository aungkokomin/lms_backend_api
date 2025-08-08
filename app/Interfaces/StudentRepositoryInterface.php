<?php

namespace App\Interfaces;

interface StudentRepositoryInterface
{
    public function list($searchValue = NULL, $start_date = NULL, $end_date = NULL);

    public function filter($searchValue);

    public function show($id);

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function getStudentByUserId($user_id);

    public function addEducation(array $data);

    public function getEducation($id);

    public function grantList($searchValue = NULL, $start_date = NULL, $end_date = NULL);

    public function grantHistoryList($searchValue = NULL, $start_date = NULL, $end_date = NULL);

    public function grantConfirmation($data);
}