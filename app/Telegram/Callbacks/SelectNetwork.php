<?php

namespace App\Telegram\Callbacks;

use App\Models\Network;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;

class SelectNetwork
{
    use Helper;

    public function __invoke(Nutgram $bot)
    {
        $networks = Network::all();
        $bot->editMessageText(
            chat_id: $bot->callbackQuery()->from->id,
            message_id: $bot->getUserData('prev_msg_id'),
            text: '🔼 *Choose the token you want to deposit*',
            parse_mode: ParseMode::MARKDOWN,
            reply_markup: $this->buildTickerButtons($networks)
                ->addRow(InlineKeyboardButton::make('❌ Cancel Bridging', callback_data: 'cancel_all'))
        );

        $bot->setUserData('bridge_opt', $bot->callbackQuery()->data);
        $bot->setUserData('action', 'select_network');
    }
}
