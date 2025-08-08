<?php

namespace App\Repositories;

use App\Interfaces\WalletTransactionRepositoryInterface;
use App\Models\WalletTransaction;
use Illuminate\Pagination\LengthAwarePaginator;

class WalletTransactionRepository implements WalletTransactionRepositoryInterface
{
    /**
     * Create a new wallet transaction
     * 
     * @param array $data
     * @return WalletTransaction
     */
    public function create(array $data): WalletTransaction
    {
        return WalletTransaction::create($data);
    }
    
    /**
     * Get transaction by ID
     * 
     * @param int $id
     * @return WalletTransaction|null
     */
    public function findById(int $id): ?WalletTransaction
    {
        return WalletTransaction::find($id);
    }
    
    /**
     * Get transactions by wallet ID
     * 
     * @param int $walletId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByWalletId(int $walletId, int $perPage = 15): LengthAwarePaginator
    {
        return WalletTransaction::where('wallet_id', $walletId)
            ->latest()
            ->paginate($perPage);
    }
    
    /**
     * Get transactions by reference type and ID
     * 
     * @param string $referenceType
     * @param int $referenceId
     * @return array
     */
    public function getByReference(string $referenceType, int $referenceId): array
    {
        return WalletTransaction::where('reference_type', $referenceType)
            ->where('reference_id', $referenceId)
            ->latest()
            ->get()
            ->toArray();
    }
    
    /**
     * Get transactions by type
     * 
     * @param string $type
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByType(string $type, int $perPage = 15): LengthAwarePaginator
    {
        return WalletTransaction::where('type', $type)
            ->latest()
            ->paginate($perPage);
    }
}