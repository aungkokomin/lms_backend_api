<?php

namespace App\Services;

use App\Interfaces\WalletTransactionRepositoryInterface;
use App\Models\WalletTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

class WalletTransactionService
{
    protected $walletTransactionRepository;
    
    /**
     * Create a new service instance.
     *
     * @param WalletTransactionRepositoryInterface $walletTransactionRepository
     * @return void
     */
    public function __construct(WalletTransactionRepositoryInterface $walletTransactionRepository)
    {
        $this->walletTransactionRepository = $walletTransactionRepository;
    }
    
    /**
     * Create a new wallet transaction
     *
     * @param array $data
     * @return WalletTransaction
     */
    public function createTransaction(array $data): WalletTransaction
    {
        return $this->walletTransactionRepository->create($data);
    }
    
    /**
     * Get wallet transaction by ID
     *
     * @param int $id
     * @return WalletTransaction|null
     */
    public function getTransaction(int $id): ?WalletTransaction
    {
        return $this->walletTransactionRepository->findById($id);
    }
    
    /**
     * Get transactions by wallet ID
     *
     * @param int $walletId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTransactionsByWalletId(int $walletId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->walletTransactionRepository->getByWalletId($walletId, $perPage);
    }
    
    /**
     * Get transactions by reference
     *
     * @param string $referenceType
     * @param int $referenceId
     * @return array
     */
    public function getTransactionsByReference(string $referenceType, int $referenceId): array
    {
        return $this->walletTransactionRepository->getByReference($referenceType, $referenceId);
    }
    
    /**
     * Get transactions by type
     *
     * @param string $type
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getTransactionsByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        return $this->walletTransactionRepository->getByType($type, $perPage);
    }
}