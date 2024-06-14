<?php

namespace App\Jobs;

use App\Models\Hash;
use App\Models\Ticker;
use App\Models\Transaction;
use App\Models\Wallet;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCoinJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Transaction $tx;
    public $network;

    /**
     * Create a new job instance.
     */
    public function __construct(Transaction $tx)
    {
        $this->tx = $tx;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $pairs = explode('_', $this->tx->pairs);
        $from = $pairs[0];
        $to = $pairs[1];

        $coin = [
            'SOL' => 'solana',
            'ETH' => 'ethereum',
            'BNB' => 'bnb',
            'TON' => 'ton',
            'ARB' => 'arbitrum',
            'XRP' => 'ripple',
            'USDT' => 'usdt',
            'BUSD' => 'busd',
            'SHIB' => 'shiba',
            'PEPE' => 'pepe'
        ];
        $target_coin = $coin[$to];

        $client = new Client();

        $wallet = Ticker::where('symbol', $to)
            ->first()
            ->network->wallet;


        $params = [
            'coin' => $target_coin,
            'pk' => $wallet->pk,
            'destination' => $this->tx->receiver,
            'amount' => (float) $this->tx->output
        ];

        if ($target_coin == 'ton') {
            $params['address'] = $wallet->address;
        }

        // Sent coin from hot wallet to customer
        $response = $client->post(env('WALLET_API_URL') . "/withdraw", [
            'form_params' => $params
        ]);

        $json = json_decode($response->getBody());

        // save hash when success
        if ($json->success) {
            Hash::create(['hash' => $json->txId, 'transaction_id' => $this->tx->id]);
        }

        if (!$json->success) {
            // Failed to sent coin
            $this->tx->update(['status' => 'Failed']);
            throw new Exception('Coin failed to sent');
        }

        $ignore_list = ['XRP', 'PEPE', 'BUSD', 'USDT', 'SHIB'];

        // Ignore to sent coin for lastly
        if (in_array($from, $ignore_list)) {
            // The transaction completed
            $this->tx->update(['status' => 'Done']);
            return;
        }

        // Sent coin from dispenser acc to hot wallet
        if ($json->success) {
            $myWallet = Ticker::where('symbol', $from)
                ->first()
                ->network->wallet;

            $fee = $this->getCryptoFee($from);
            $final_amount = $this->tx->amount - $fee;
            $target_coin = $coin[$from];

            $params = [
                'coin' => $target_coin,
                'pk' => $this->tx->dispenser->pk,
                'destination' => $myWallet->address,
                'amount' => (float) $final_amount
            ];

            if ($target_coin == 'ton') {
                $params['address'] = $myWallet->address;
            }

            $response = $client->post(env('WALLET_API_URL') . "/withdraw", [
                'form_params' => $params
            ]);

            // The transaction completed
            $this->tx->update(['status' => 'Done']);
        }
    }

    function getCryptoFee($symbol)
    {
        $client = new Client();

        $data = $client->get(env('WALLET_API_URL') . '/estimate?coin=' . $symbol);
        $result = json_decode($data->getBody()->getContents());

        return $result->fee;
    }
}
