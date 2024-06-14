<?php

namespace Database\Seeders;

use App\Models\Wallet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Wallet::create([
            'name' => 'Wallet ETH',
            'address' => '0x',
            'balance' => 0,
            'pk' => '',
            'network_id' => 2
        ]);

        Wallet::create([
            'name' => 'Wallet BNB',
            'address' => '0x',
            'balance' => 0,
            'pk' => '',
            'network_id' => 1
        ]);

        Wallet::create([
            'name' => 'Wallet ARB',
            'address' => '0x',
            'balance' => 0,
            'pk' => '',
            'network_id' => 4
        ]);

        Wallet::create([
            'name' => 'Wallet Solana',
            'address' => 'S',
            'balance' => 0,
            'pk' => '',
            'network_id' => 3
        ]);

        Wallet::create([
            'name' => 'Wallet XRP',
            'address' => 'R',
            'balance' => 0,
            'pk' => '',
            'network_id' => 5
        ]);

        Wallet::create([
            'name' => 'Wallet TON',
            'address' => 'E',
            'balance' => 0,
            'pk' => '',
            'network_id' => 6
        ]);
    }
}
