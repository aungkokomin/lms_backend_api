<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Interfaces\PaymentRepositoryInterface;

class PaymentRepository implements PaymentRepositoryInterface
{

    protected $payment;

    public function __construct(Payment $payment) {
        $this->payment = $payment;
    }
    // Add your repository methods here
    public function list()
    {   
        return $this->payment->all();
    }

    public function create($data)
    {
        return $this->payment->create([
            'payment_method' => $data['payment_method'] ?? null,
            'amount' => $data['amount'] ?? 0,
            'status' => $data['status'] ?? Payment::STATUS_PENDING,
            'payment_reference' => $data['payment_reference'] ?? null,
            'payment_details' => $data['payment_details'] ?? null,
            'transaction_id' => $data['transaction_id'] ?? null,
            'currency' => $data['currency'] ?? 'USD',
            'payment_date' => $data['payment_date'] ?? null,
            'completed_date' => $data['completed_date'] ?? null,
        ]);
    }

    public function update($data, $id)
    {
        return $this->payment->where('id', $id)->update($data);
    }

    public function show($id)
    {
        return $this->payment->with('image')->findOrFail($id);
    }

    public function delete($id)
    {
        return $this->payment->where('id', $id)->delete();
    }

    public function confirm($data)
    {
        $payment = $this->payment->findOrFail($data['payment_id']);
        $payment->update(['status' => Payment::STATUS_COMPLETED]);
        return $payment->fresh();
    }

    public function studentEnrollPayments($data)
    {
        return $this->payment->create($data);
    }

    public function showPendingPayment($id)
    {
        return $this->payment->where('id', $id)->where('status', Payment::STATUS_PENDING)->first();
    }
}