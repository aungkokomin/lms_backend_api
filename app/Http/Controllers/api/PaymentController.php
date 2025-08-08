<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService) {
        $this->paymentService = $paymentService;
    }
    //
    public function index()
    {
        //
        try {
            return response()->json([
                'data' => $this->paymentService->list(),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function show(string $id)
    {
        //
        try {
            return response()->json([
                'data' => $this->paymentService->show($id),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function studentEnrollPayments(Request $request)
    {
        //
        try {
            return response()->json([
                'data' => $this->paymentService->studentEnrollPayments($request->all()),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function confirmPayment(Request $request)
    {
        //
        try {
            return response()->json([
                'data' => $this->paymentService->confirmPayment($request->all()),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function rejectPayment(Request $request)
    {
        //
        try {
            return response()->json([
                'data' => $this->paymentService->reject($request->all()),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function stripeSuccess(Request $request){
        try {
            $data = $request->validate([
                'session_id' => 'required|string'
            ]);

            return response()->json([
                'data' => $data['session_id'],
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function stripeCancel(Request $request){
        try {
            $data = $request->validate([
                'session_id' => 'required|string'
            ]);
            return response()->json([
                'data' => $this->paymentService->stripeCancel($data),
                'status' => 200
            ],200);
        } catch (\Exception $e) {
            return response()->json([
                'data' => $e->getMessage(),
                'status' => 400
            ],400);
        }
    }

    public function getTransactionStatus(Request $request)
    {
        $txHash = $request->txhash;
        $apiKey = config('services.etherscan.API_KEY');
        $url = config('services.etherscan.URL');
        $response = Http::get($url, [
            'module' => 'proxy',
            'action' => 'eth_getTransactionReceipt',
            'txhash' => $txHash,
            'apikey' => $apiKey
        ]);

        $result = $response->json()['result'] ?? null;

        self::convertHextoReadable($result);
    }

    private function convertHextoReadable($result)
    {
        $status         = $result['status'] === '0x1' ? 'Success' : 'Failed';
        $blockNumber    = hexdec($result['blockNumber']);
        $gasUsed        = hexdec($result['gasUsed']);
        $gasPrice       = hexdec($result['effectiveGasPrice']); // in Wei
        $bnbUsed        = $gasUsed * $gasPrice / pow(10, 18); // convert Wei to BNB

        echo "Transaction Status: $status\n";
        echo "Block Number: $blockNumber\n";
        echo "Gas Used: $gasUsed\n";
        echo "Gas Price (Wei): $gasPrice\n";
        echo "Total Fee (BNB): $bnbUsed\n";

        echo "From: " . $result['from'] . "\n";
        echo "To: " . $result['to'] . "\n";
        echo "Tx Hash: " . $result['transactionHash'] . "\n";

    }
}
