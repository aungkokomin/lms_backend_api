<?php

namespace App\Interfaces;

interface OrderRepositoryInterface
{
    public function list($searchValue = NULL, $start_date = NULL, $end_date = NULL);

    public function get($id);

    public function listByUser($user_id);
    
    public function create(array $data);
    
    public function update($id, array $data);
    
    public function delete($id);
    
}