<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService) {
        $this->walletService = $walletService;
    }

    /**
     * Get user wallet information
     * 
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = Auth::user()->id;
            $wallet = $this->walletService->getOrCreateWallet($userId,$request->currency);
            
            return response()->json([
                'status' => 200,
                'data' => $wallet
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new wallet
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                "user_id" => "required|integer",
                "balance" => "required|numeric|min:0",
                "currency" => "required|string|max:3",
                "is_active" => "required|boolean",
            ]);
            
            $userId = $request->user_id;
            $wallet = $this->walletService->getOrCreateWallet($userId);
            
            return response()->json([
                'status' => 201,
                'message' => 'Wallet created successfully',
                'data' => $wallet
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => 400,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Deposit funds to wallet
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function deposit(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'user_id' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string',
                'reference_type' => 'nullable|string',
                'reference_id' => 'nullable|integer'
            ]);
            
            $result = $this->walletService->deposit(
                $request->user_id,
                $request->amount,
                $request->description,
                $request->reference_type,
                $request->reference_id
            );
            
            return response()->json([
                'message' => 'Deposit successful',
                'data' => $result,
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }
    }
    
    /**
     * Withdraw funds from wallet
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function withdraw(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'user_id' => 'required|integer',
                'amount' => 'required|numeric|min:0.01',
                'description' => 'nullable|string',
                'reference_type' => 'nullable|string',
                'reference_id' => 'nullable|integer'
            ]);
            
            $result = $this->walletService->withdraw(
                $request->user_id,
                $request->amount,
                $request->description,
                $request->reference_type,
                $request->reference_id
            );
            
            return response()->json([
                'message' => 'Withdrawal successful',
                'data' => $result,
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }
    }
    
    /**
     * Get wallet transaction history
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function transactions(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'user_id' => 'required|integer',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);
            
            $perPage = $request->per_page ?? 15;
            $transactions = $this->walletService->getTransactions($request->user_id, $perPage);
            
            if ($transactions === null) {
                return response()->json([
                    'message' => 'Wallet not found for this user',
                    'status' => 404
                ], 404);
            }
            
            return response()->json([
                'data' => $transactions,
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }
    }
    
    /**
     * Get wallet balance
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function balance(Request $request): JsonResponse
    {
        try {
            $this->validate($request, [
                'user_id' => 'required|integer'
            ]);
            
            $wallet = $this->walletService->getOrCreateWallet($request->user_id);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'balance' => $wallet->balance,
                    'currency' => $wallet->currency
                ],
                'status' => 200
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'status' => 400
            ], 400);
        }
    }
    
}