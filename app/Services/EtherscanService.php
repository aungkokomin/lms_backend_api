<?php
// app/Services/EtherscanService.php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EtherscanService
{
    public function getTransactionStatusService($txHash)
    {
        $apiKey = config('services.etherscan.API_KEY');
        $url = config('services.etherscan.URL');
        $response = Http::get($url, [
            'module' => 'proxy',
            'action' => 'eth_getTransactionReceipt',
            'txhash' => $txHash,
            'apikey' => $apiKey
        ]);

        return $response->json();
    }
}
