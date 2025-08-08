<?php

namespace App\Interfaces;

interface InstructorRepositoryInterface
{
    public function list();

    public function create(array $data);

    public function show($id);
}