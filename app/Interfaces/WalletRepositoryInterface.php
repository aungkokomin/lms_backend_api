<?php

namespace App\Interfaces;

interface WalletRepositoryInterface
{
    // Add your Interfaces methods here
    public function findByUserId(int $userId);
    
    public function create(array $data);
    
    public function updateBalance(object $wallet, float $newBalance);
    
    public function createTransaction(array $data);
    public function getTransactionsByWalletId(int $walletId, int $perPage = 15);



}