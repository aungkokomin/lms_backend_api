<?php

namespace App\Interfaces;

use App\Models\WalletTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

interface WalletTransactionRepositoryInterface
{
    /**
     * Create a new wallet transaction
     * 
     * @param array $data
     * @return WalletTransaction
     */
    public function create(array $data): WalletTransaction;
    
    /**
     * Get transaction by ID
     * 
     * @param int $id
     * @return WalletTransaction|null
     */
    public function findById(int $id): ?WalletTransaction;
    
    /**
     * Get transactions by wallet ID
     * 
     * @param int $walletId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByWalletId(int $walletId, int $perPage = 15): LengthAwarePaginator;
    
    /**
     * Get transactions by reference type and ID
     * 
     * @param string $referenceType
     * @param int $referenceId
     * @return array
     */
    public function getByReference(string $referenceType, int $referenceId): array;
    
    /**
     * Get transactions by type
     * 
     * @param string $type
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, int $perPage = 15): LengthAwarePaginator;
}