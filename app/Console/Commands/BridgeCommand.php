<?php

namespace App\Console\Commands;

use App\Jobs\SendCoinJob;
use App\Models\Network;
use App\Models\Transaction;
use App\Models\Wallet;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Warn;
use phpDocumentor\Reflection\Types\Boolean;

class BridgeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:bridge';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bridge all coin';

    public $api;

    public function __construct()
    {
        $this->api = [
            "ETH" => [
                "url" => env('APP_ENV') == 'local'
                    ? "https://api-sepolia.etherscan.io/api"
                    : "https://api.etherscan.io/api",
                "api_key" => env('ETHSCAN_API_KEY')
            ],
            "BNB" => [
                "url" => env('APP_ENV') == 'local'
                    ? "https://api-testnet.bscscan.com/api"
                    : "https://api.bscscan.com/api",
                "api_key" => env('BSCSCAN_API_KEY')
            ],
        ];
        parent::__construct(...func_get_args());
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $transactions = Transaction::where('status', 'Waiting')->get();

        foreach ($transactions as $tx) {
            $pairs = explode('_', $tx->pairs);
            $from = $pairs[0];

            $tokens = ['PEPE', 'SHIB', 'USDT', 'BUSD'];
            if (in_array($from, $tokens)) {
                $wallet = Network::find(2)->wallet;
                $sended = $this->isTokenSended($from, $wallet->address, $tx->amount);

                if ($sended) {
                    $this->info('Sending job');
                    $tx->update(['status' => 'Processing']);
                    SendCoinJob::dispatch($tx);
                }

                continue;
            }

            // If from XRP
            if ($from === 'XRP') {
                $wallet = Network::find(5)->wallet;
                $sended = $this->isRippleSended(address: $wallet->address, tag: $tx->id, amount: $tx->amount);

                if ($sended) {
                    $this->info('Sending job');
                    $tx->update(['status' => 'Processing']);
                    SendCoinJob::dispatch($tx);
                }

                continue;
            }

            // Others (ETH, BNB, ARB, SOL)
            $balance = $this->checkBalance($from, $tx->dispenser->address);

            if ((float) $balance >= $tx->amount) {
                $this->info('Sending job');
                $tx->update(['status' => 'Processing']);
                SendCoinJob::dispatch($tx);
            }
        }
    }

    protected function checkBalance($symbol, $address)
    {
        if ($symbol === 'ETH' || $symbol === 'BNB' || $symbol === "ARB") {
            $response = $this->getEVMAccount($symbol, $address);

            if ($response->status) {
                return $response->result;
            }
        }

        if ($symbol === 'SOL') {
            $response = $this->getSolBalance($address);

            if ($response->success) {
                return $response->balance;
            }
        }
    }

    protected function isRippleSended(string $address, int $tag, float $amount)
    {
        $client = new Client();
        $data = $client->get(env('WALLET_API_URL') . '/ripple/tx?address=' . $address);
        $response = json_decode($data->getBody());

        foreach ($response as $val) {
            if (!isset($val->tag)) continue;
            if ($val->tag == $tag && $val->destination === $address && $val->amount >= $amount) {
                return true;
            }
        }

        return false;
    }

    protected function getSolBalance($address)
    {
        $client = new Client();
        $data = $client->get(env('WALLET_API_URL') . '/balance/solana?address=' . $address);
        $result = json_decode($data->getBody());
        return $result;
    }

    protected function getEVMAccount($network, $address)
    {
        $client = new Client();

        // Parameters for the GET request
        $params = [
            'module' => 'account',
            'action' => 'balance',
            'address' => $address,
            'apikey' => $this->api[$network]["api_key"],
        ];

        try {
            // Make GET request using Guzzle
            $response = $client->request('GET', $this->api[$network]["url"], [
                'query' => $params,
            ]);

            // Get response body
            $body = $response->getBody();

            // Output response body
            return json_decode($body);
        } catch (\Throwable $e) {
            // Handle exception
            echo 'Error: ' . $e->getMessage();
        }
    }

    protected function isTokenSended(string $symbol, string $address, float $amount): bool
    {
        $tx = $this->getTokenTx($symbol);

        $ca = [
            "PEPE" => env('APP_ENV') == 'local'
                ? '0xa7ddC63d7A99827f569761ec0F2d89DF5C60Fa56'
                : '0x6982508145454Ce325dDbE47a25d4ec3d2311933',
            "SHIB" => env('APP_ENV') == 'local'
                ? '0x5e184F116e8bf195F75D64C0B4070a5ACB3cF1eC'
                : '0x95aD61b0a150d79219dCF64E1E6Cc01f0B64C4cE',
            "USDT" => env('APP_ENV') == 'local'
                ? '0x51eF9Ae8f376A39A8fd18D96888c7Dc05C703747'
                : '0xdAC17F958D2ee523a2206206994597C13D831ec7',
            "BUSD" => env('APP_ENV') == 'local'
                ? '0xeD24FC36d5Ee211Ea25A80239Fb8C4Cfd80f12Ee'
                : '0xe9e7CEA3DedcA5984780Bafc599bD69ADd087D56'
        ];

        $token_addr = strtolower($ca[$symbol]);

        foreach ($tx as $val) {
            if ($val->value == 0) continue;

            $value = $val->value / 10 ** $val->tokenDecimal;

            if (
                $val->contractAddress == $token_addr
                && $val->to == strtolower($address)
                && $value == $amount
            ) {
                return true;
            }
        }

        return false;
    }

    protected function getTokenTx($symbol)
    {
        $client = new Client();

        $network = [
            'PEPE' => 'ETH',
            'USDT' => 'ETH',
            'SHIB' => 'ETH',
            'BUSD' => 'BNB'
        ];


        $wallet = Wallet::find($network == 'ETH' ? 1 : 2);

        // Parameters for the GET request
        $params = [
            'module' => 'account',
            'action' => 'tokentx',
            'address' => $wallet->address,
            'apikey' => $this->api[$network[$symbol]]["api_key"],
            'start' => 0,
            'endblock' => 999999999,
            'sort' => 'desc',
            'page' => 1,
            'offset' => 10
        ];

        try {
            // Make GET request using Guzzle
            $response = $client->request('GET', $this->api[$network[$symbol]]["url"], [
                'query' => $params,
            ]);

            // Get response body
            $body = $response->getBody();

            // Output response body
            $data = json_decode($body);
            return $data->result;
        } catch (\Throwable $e) {
            // Handle exception
            echo 'Error: ' . $e->getMessage();
        }
    }
}
