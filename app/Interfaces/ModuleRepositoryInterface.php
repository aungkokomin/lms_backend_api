<?php

namespace App\Interfaces;

interface ModuleRepositoryInterface
{
    public function getAllModules($searchValue = null);
    public function getModuleById($id);
    public function createModule(array $data);
    public function updateModule($id, array $data);
    public function deleteModule($id);
    public function getByCourse($courseId);
}
