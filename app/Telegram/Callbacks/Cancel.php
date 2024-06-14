<?php

namespace App\Telegram\Callbacks;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class Cancel
{
    public function __invoke(Nutgram $bot)
    {
        $bot->editMessageText(
            text: "<b>Operation canceled</b>",
            chat_id: $bot->user()->id,
            message_id: $bot->getUserData('prev_msg_id'),
            parse_mode: ParseMode::HTML
        );
    }
}
