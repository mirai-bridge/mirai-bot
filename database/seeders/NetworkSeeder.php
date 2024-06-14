<?php

namespace Database\Seeders;

use App\Models\Network;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NetworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Network::insert([
            ['name' => 'BNB'],
            ['name' => 'Ethereum'],
            ['name' => 'Solana'],
            ['name' => 'Arbitrum'],
            ['name' => 'Ripple'],
            ['name' => 'TON'],
        ]);
    }
}
