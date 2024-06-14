<?php

namespace App\Telegram\Callbacks;

use App\Models\Ticker;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class EnterAmount
{
    public function __invoke(Nutgram $bot)
    {
        $from = $bot->getUserData('from_token');
        $to = $bot->getUserData('to_token');
        $fromTicker = Ticker::find($from)->name;
        $toTicker = Ticker::find($to)->name;

        $text = <<<EOD
        <b>Enter the amount of $fromTicker that you have and want to exchange for $toTicker.</b>

        Example: If you have 0.1 ETH and want to convert it to USDT, just type 0.1.
        EOD;

        $message = $bot->sendMessage($text, parse_mode: ParseMode::HTML);

        $bot->setUserData('ask_msg_id', $message->message_id);
        $bot->setUserData('input', 'amount');
    }
}
