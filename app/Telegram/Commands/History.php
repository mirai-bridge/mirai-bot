<?php

namespace App\Telegram\Commands;

use App\Models\Customer;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class History
{
    public function __invoke(Nutgram $bot)
    {
        $orders = Customer::findByUsername($bot->user()->username)
            ->transactions()
            ->latest()
            ->limit(5)
            ->get();

        $list = '';
        foreach ($orders as $tx) {
            $pairs = explode('_', $tx->pairs);
            $status = $tx->status == 'Waiting' ? 'Waiting Deposit' : $tx->status;
            $list .= $tx->amount . ' ' . $pairs[0] . " → {$tx->output} " . $pairs[1] . ' | <b>' . $status . "</b> | /order_{$tx->id}\n";
        }

        $text = <<<EOD
        📑 Last Orders

        $list
        EOD;

        $bot->sendMessage($text, parse_mode: ParseMode::HTML);
    }
}
