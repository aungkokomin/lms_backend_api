<?php

namespace App\Interfaces;
Interface PaymentRepositoryInterface
{
    public function list();
    public function create($data);
    public function update($data, $id);
    public function show($id);
    public function delete($id);
    public function confirm($data);
    public function studentEnrollPayments($data);
    public function showPendingPayment($id);
}