<?php

namespace App\Console\Commands;

use App\Models\Ticker;
use Illuminate\Console\Command;

class UpdatePriceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update price of all crypto.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $crypto = Ticker::pluck('ids')->toArray();
        $ids = implode(',', $crypto);

        // Inisialisasi cURL
        $curl = curl_init();

        // URL yang akan diakses
        $url = "https://api.coingecko.com/api/v3/simple/price?ids={$ids}&vs_currencies=usd";

        // Set URL dan konfigurasi lainnya
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true
        ));

        // Eksekusi permintaan cURL dan simpan respons
        $response = curl_exec($curl);

        if ($response) {
            // Decode respons JSON
            $data = json_decode($response, true);

            foreach ($data as $key => $coin) {
                Ticker::where('ids', $key)->update([
                    'usd_price' => $coin['usd']
                ]);
            }
        }

        $this->info('Updated!');
    }
}
