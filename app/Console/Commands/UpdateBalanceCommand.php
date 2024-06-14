<?php

namespace App\Console\Commands;

use App\Models\Wallet;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class UpdateBalanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all wallet balance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $wallets = Wallet::all();

        foreach ($wallets as $wallet) {
            if (
                $wallet->network->name == 'Ethereum'
                || $wallet->network->name == 'BNB'
                || $wallet->network->name == 'Arbitrum'
            ) {

                $balance = $this->getEvmBalance($wallet->network->name, $wallet->address);
                $wallet->update(['balance' => $balance]);
            }

            if ($wallet->network->name == 'Solana') {
                $balance = $this->getSolanaBalance($wallet->address);
                $wallet->update(['balance' => $balance]);
            }

            if ($wallet->network->name == 'Ripple') {
                $balance = $this->getRippleBalance($wallet->address);
                $wallet->update(['balance' => $balance]);
            }
        }

        $this->info('OK');
    }

    function getEvmBalance($network, $addres)
    {
        $api = [
            "Ethereum" => [
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
            "Arbitrum" => [
                "url" => env('APP_ENV') == 'local'
                    ? "https://api-sepolia.arbiscan.io/api"
                    : "https://api.arbiscan.io/api",
                "api_key" => env('ARBCAN_API_KEY')
            ],
        ];

        $client = new Client();
        $response = $client->get($api[$network]['url'], [
            'query' => [
                'module' => 'account',
                'action' => 'balance',
                'address' => $addres,
                'apikey' => $api[$network]['api_key']
            ]
        ]);
        $data = json_decode($response->getBody());

        if (!$data->status) {
            return 0;
        }

        $balance = $data->result / 10 ** 18;

        return number_format($balance, 4);
    }

    function getSolanaBalance($addres)
    {
        $client = new Client();
        $response = $client->get(env('WALLET_API_URL') . '/balance/solana?address=' . $addres);
        $data = json_decode($response->getBody());

        if (!$data->success) {
            return 0;
        }

        return $data->balance;
    }

    function getRippleBalance($addres)
    {
        $client = new Client();
        $response = $client->get(env('WALLET_API_URL') . '/balance/ripple?address=' . $addres);
        $data = json_decode($response->getBody());

        if (!$data->success) {
            return 0;
        }

        return $data->balance;
    }
}
