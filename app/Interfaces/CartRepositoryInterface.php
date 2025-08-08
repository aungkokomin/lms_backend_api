<?php

namespace App\Interfaces;

interface CartRepositoryInterface
{
    public function getCartDataByUser($user_id);
    
    public function create($user_id);

    public function edit($user_id);

    public function delete($id);

    public function deleteAllCart($user_id);

    public function addCartItems(array $data);

    public function getCart($id);

    public function minusCartItems($data);
}