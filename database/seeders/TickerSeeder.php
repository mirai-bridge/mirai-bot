<?php

namespace Database\Seeders;

use App\Models\Ticker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TickerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Ticker::insert([
            [
                'name' => 'BNB (BEP20)',
                'symbol' => 'BNB',
                'ids' => 'binancecoin',
                'minimum' => 0.1,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 1,
            ],
            [
                'name' => 'BUSD (BEP20)',
                'symbol' => 'BUSD',
                'ids' => 'binance-usd',
                'minimum' => 0.1,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 1,
            ],
            [
                'name' => 'ETH (ERC20)',
                'symbol' => 'ETH',
                'ids' => 'ethereum',
                'minimum' => 0.05,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 2,
            ],
            [
                'name' => 'USDT (ERC20)',
                'symbol' => 'USDT',
                'ids' => 'tether',
                'minimum' => 50,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 2,
            ],
            [
                'name' => 'Solana',
                'symbol' => 'SOL',
                'ids' => 'solana',
                'minimum' => 0.5,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 3,
            ],
            [
                'name' => 'TON',
                'symbol' => 'TON',
                'ids' => 'the-open-network',
                'minimum' => 1,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 6,
            ],
            [
                'name' => 'ARB (ARB20)',
                'symbol' => 'ARB',
                'ids' => 'arbitrum',
                'minimum' => 10,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 4,
            ],
            [
                'name' => 'PEPE (ERC20)',
                'symbol' => 'PEPE',
                'ids' => 'pepe',
                'minimum' => 1000000,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 2,
            ],
            [
                'name' => 'Ripple',
                'symbol' => 'XRP',
                'ids' => 'ripple',
                'minimum' => 20,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 5,
            ],
            [
                'name' => 'Shiba Inu',
                'symbol' => 'SHIB',
                'ids' => 'shiba-inu',
                'minimum' => 200000,
                'usd_price' => 0,
                'status' => 'Active',
                'network_id' => 2,
            ],
        ]);
    }
}
