<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Commission;
use App\Models\Referral;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\WalletService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateDashboardController extends Controller
{
    //
    private $walletService;

    public function __construct(WalletService $walletService) {
        $this->walletService = $walletService;
    }
    public function dashboard()
    {
        try{
            $user = Auth::user();
            $result['totalCommissionFee'] = Commission::where('user_id',$user->id)->sum('commission_amount');
            $wallet = $this->walletService->getOrCreateWallet($user->id);
            $result['balance'] = $wallet->balance ?? 0;
            $result['withdraw_amt'] = WalletTransaction::where('wallet_id',$wallet->id)->where('type','withdrawal')->sum('amount');
            $result['referred_accounts'] = Referral::where('referrer_id',$user->referral_id)->count();
            
            return response()->json([
                'data' => $result,
                'status' => 200
            ],200);
        } catch (Exception $e){
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
        
    }
}
