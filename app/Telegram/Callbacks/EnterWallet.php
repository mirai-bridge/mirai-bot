<?php

namespace App\Telegram\Callbacks;

use App\Models\Ticker;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class EnterWallet
{
    public function __invoke(Nutgram $bot)
    {
        $to = $bot->getUserData('to_token');
        $toTicker = Ticker::find($to);
        $message = $bot->sendMessage(
            text: "<b>Enter your {$toTicker->network->name} ({$toTicker->name}) wallet address</b>",
            parse_mode: ParseMode::HTML
        );

        $bot->setUserData('ask_msg_id', $message->message_id);
        $bot->setUserData('input', 'wallet');
    }
}
