<?php

namespace App\Telegram\Callbacks;

use App\Models\Customer;
use App\Models\Dispenser;
use App\Models\Network;
use App\Models\Ticker;
use App\Models\Transaction;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use SergiX44\Nutgram\Nutgram;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\PublicKeyLoader;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class ProcessBridge
{
    public function __invoke(Nutgram $bot)
    {
        $bot->editMessageText(
            chat_id: $bot->callbackQuery()->from->id,
            message_id: $bot->getUserData('prev_msg_id'),
            text: 'Creating order...'
        );

        $customer = Customer::where('telegram_id', $bot->user()->id)->first();
        $from = $this->getFromToken($bot);
        $to = $this->getOutputToken($bot);
        $amount = $bot->getUserData('amount');
        $output = $this->convertCrypto($from->ids, $to->ids, $amount);
        $revenue = $this->convertCryptoToFiat($from->ids, $amount * 0.01);
        $receive = $bot->getUserData('wallet');
        $deposit = $this->requestDepositAccount($customer, $from, $bot);
        $deposit_address = $deposit->address;

        $tx = Transaction::create([
            'pairs' => $from->symbol . '_' . $to->symbol,
            'receiver' => $receive,
            'amount' => $amount,
            'output' => $output,
            'revenue' => $revenue,
            'status' => 'Waiting',
            'customer_id' => $customer->id,
            'dispenser_id' => $deposit->id
        ]);

        $xrp_note = $from->symbol == 'XRP' ? "<b>Destination Tag/MEMO:</b> <code>{$tx->id}</code>" : '';

        $text = <<<EOD
        <b>Bridging ID:</b> <code>$tx->id</code> <b>— Bridging Transaction started:</b>
    
        • Send: $amount $from->symbol ($from->name)
        • Receive: $output $to->symbol
        • Receive Wallet: <code>$receive</code>

        ➡️ <b>Please transfer $amount $from->symbol ($from->name) to start bridging process.</b> 
        <b>Deposit Wallet:</b> 
        <code>$deposit_address</code>
        $xrp_note

        <b>You will get $output $to->symbol, for your $amount $from->symbol ($from->name).</b>

        <b>Note:</b> <i>You must complete this trade within a maximum of 30 minutes. Fees can vary between</i> <b>0.1%</b> and <b>0.5%</b>.

        <b>📡 Status: ⏳ Waiting Deposit</b>
        EOD;

        $bot->setUserData('prev_tx', $tx->id);

        $bot->editMessageText(
            chat_id: $bot->callbackQuery()->from->id,
            message_id: $bot->getUserData('prev_msg_id'),
            text: $text,
            parse_mode: ParseMode::HTML,
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('🔎 Refresh Status', callback_data: 'refStatus'))
        );
    }

    function requestDepositAccount($customer, $from, Nutgram $bot)
    {
        // For ripple deposit
        if ($from->symbol === 'XRP') {
            $wallet = Network::find(5)->wallet;
            return $wallet;
        }

        // For ERC20 token deposit
        $tokens = ['PEPE', 'SHIB', 'USDT'];
        if (in_array($from->symbol, $tokens)) {
            $wallet = Network::find(2)->wallet;
            return $wallet;
        }

        // For BUSD deposit
        if ($from->symbol === 'BUSD') {
            $wallet = Network::find(1)->wallet;
            return $wallet;
        }

        $account = $customer->dispenser()->where('ticker_id', $from->id)->first();

        if ($account) {
            $account->update(['used' => true]);
            return $account;
        }

        try {
            $type = [
                'ETH' => 'evm',
                'BNB' => 'evm',
                'ARB' => 'evm',
                'SOL' => 'solana',
                'XRP' => 'ripple',
            ];

            $URL = env('WALLET_API_URL') . '/wallet/create?type=' . $type[$from->symbol];

            $client = new Client();
            $response = $client->request('GET', $URL);

            $body = $response->getBody()->getContents();
            $result = json_decode($body);

            $data = Dispenser::create([
                'address' => $result->address,
                'pk' => $this->encryptRSA($result->pk),
                'used' => true,
                'ticker_id' => $from->id,
                'customer_id' => $customer->id
            ]);

            return $data;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());
            $bot->editMessageText(
                chat_id: $bot->callbackQuery()->from->id,
                message_id: $bot->getUserData('prev_msg_id'),
                text: 'Error, can not process your order'
            );
        }
    }

    public function encryptRSA($data)
    {
        $publicKey = <<<EOD
        -----BEGIN RSA PUBLIC KEY-----
        MIIBCgKCAQEA3s93s7uut898a5qBPrfiydTSX+lVobiefzBfzyyhjxbZyAnJ+Xo8
        /QbH8MSQ+ioC6irMO78xpB5BbLlbClizluhI5rGkcwe8wQFcA2t+j2bOdRn3gfaE
        bz9aMWJpokjoueRm7yg1qc08dR18CgOJk+xiY8MBzQtpJIPy4qHaH3eDf3J1tZUR
        vStDB8pah6PczFisncTivAuUPi34xsDp1fn7+bq7V9bItxDfTgV213aHDq9KMUzL
        eKqF9ymcMpbDShGbEFQNoqH+at+ldEsxbEYs7bH6LZgJn2J3/u5h7SaTFBMklpLI
        32soO9Q77VqUwzWIhqiMTY88MWdxacwKXwIDAQAB
        -----END RSA PUBLIC KEY-----
        EOD;

        $key = PublicKeyLoader::load($publicKey)
            ->withPadding(RSA::ENCRYPTION_OAEP);

        // Encrypt the data
        $encrypted = $key->encrypt($data);

        return base64_encode($encrypted);
    }

    function getFromToken(Nutgram $bot)
    {
        $id = $bot->getUserData('from_token');
        $from = Ticker::find($id);

        return $from;
    }

    function getOutputToken(Nutgram $bot)
    {
        $id = $bot->getUserData('to_token');
        $to = Ticker::find($id);

        return $to;
    }

    function convertCrypto($from, $target, $amount)
    {
        $coin1 = Ticker::where('ids', $from)->first();
        $coin2 = Ticker::where('ids', $target)->first();

        if (!$coin1 || !$coin2) {
            return 0;
        }

        // Hitung konversi mata uang
        $result = ($amount * $coin1->usd_price) / $coin2->usd_price;

        $revenue = $result * 0.001; // 0.1%
        $fee = $this->getCryptoFee($coin1->symbol);

        // Format hasil
        $formattedResult = number_format($result - ($revenue + $fee), 6);

        // Kembalikan hasil
        return (float) str_replace(',', '', $formattedResult);
    }

    function convertCryptoToFiat($target, $amount)
    {
        $crypto = Ticker::where('ids', $target)->first();

        if (!$crypto) {
            return 0;
        }

        $usd = $crypto->usd_price;

        $in_usd = (float) $amount * $usd;

        // Kembalikan hasil
        return $in_usd;
    }

    function getCryptoFee($symbol)
    {
        $client = new Client();

        $data = $client->get(env('WALLET_API_URL') . '/estimate?coin=' . $symbol);
        $result = json_decode($data->getBody()->getContents());

        return $result->fee;
    }
}
