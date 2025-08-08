<?php

namespace App\Services;

use App\Interfaces\WalletRepositoryInterface;
use App\Interfaces\WalletServiceInterface;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Exception;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Currency;

class WalletService
{
    protected $walletRepository;
    
    public function __construct(WalletRepositoryInterface $walletRepository)
    {
        $this->walletRepository = $walletRepository;
    }
    
    public function getOrCreateWallet(int $userId,string $currency = 'usd'): Wallet
    {
        $wallet = $this->walletRepository->findByUserId($userId);
        $user = User::findOrFail($userId);
        if (!$wallet) {
            if($user->hasVerifiedEmail()){
                $wallet = $this->walletRepository->create([
                    'user_id' => $userId,
                    'balance' => 0,
                    'currency' => $currency,
                    'is_active' => true
                ]);
            }else{
                throw new Exception("User email not verified");
            }
        }
        
        return $wallet;
    }
    
    public function deposit(int $userId, float $amount, ?string $description, ?string $referenceType = null, ?int $referenceId = null): array
    {
        $wallet = $this->getOrCreateWallet($userId);
        $newBalance = $wallet->balance + $amount;
        
        $this->walletRepository->updateBalance($wallet, $newBalance);
        
        $transaction = $this->walletRepository->createTransaction([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => 'deposit',
            'description' => $description ?? 'Wallet deposit',
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);
        
        return [
            'wallet' => $wallet->refresh(),
            'transaction' => $transaction
        ];
    }

    public function withdrawRequest(int $userId, float $amount, ?string $description, ?string $referenceType = null, ?int $referenceId = null): array
    {
        $wallet = $this->getOrCreateWallet($userId);
        
        if ($wallet->balance < $amount) {
            throw new Exception('Insufficient funds');
        }
        
        $transaction = $this->walletRepository->createTransaction([
            'wallet_id' => $wallet->id,
            'amount' => -$amount,
            'type' => 'withdrawal',
            'description' => $description ?? 'Wallet withdrawal',
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);
        
        return [
            'wallet' => $wallet->refresh(),
            'transaction' => $transaction
        ];
    }
    
    public function withdraw(int $userId, float $amount, ?string $description, ?string $referenceType = null, ?int $referenceId = null): array
    {
        $wallet = $this->getOrCreateWallet($userId);
        
        if ($wallet->balance < $amount) {
            throw new Exception('Insufficient funds');
        }
        
        $newBalance = $wallet->balance - $amount;
        $this->walletRepository->updateBalance($wallet, $newBalance);
        
        $transaction = $this->walletRepository->createTransaction([
            'wallet_id' => $wallet->id,
            'amount' => -$amount,
            'type' => 'withdrawal',
            'description' => $description ?? 'Wallet withdrawal',
            'reference_type' => $referenceType,
            'reference_id' => $referenceId
        ]);
        
        return [
            'wallet' => $wallet->refresh(),
            'transaction' => $transaction
        ];
    }
    
    public function getTransactions(int $userId, int $perPage = 15)
    {
        $wallet = $this->walletRepository->findByUserId($userId);
        
        if (!$wallet) {
            return null;
        }
        
        return $this->walletRepository->getTransactionsByWalletId($wallet->id, $perPage);
    }
}