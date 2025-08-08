<?php

namespace App\Repositories;

use App\Interfaces\WalletRepositoryInterface;
use App\Models\Wallet;
use App\Models\WalletTransaction;

class WalletRepository implements WalletRepositoryInterface
{
    public function findByUserId(int $userId): ?Wallet
    {
        return Wallet::where('user_id', $userId)->first();
    }
    
    public function create(array $data): Wallet
    {
        return Wallet::create($data);
    }
    
    public function updateBalance(object $wallet, float $newBalance): bool
    {
        $wallet->balance = $newBalance;
        return $wallet->save();
    }
    
    public function createTransaction(array $data): WalletTransaction
    {
        return WalletTransaction::create($data);
    }
    
    public function getTransactionsByWalletId(int $walletId, int $perPage = 10)
    {
        return WalletTransaction::where('wallet_id', $walletId)
            ->latest()
            ->paginate($perPage);
    }
}