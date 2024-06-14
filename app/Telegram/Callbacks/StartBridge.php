<?php

namespace App\Telegram\Callbacks;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartBridge
{
    public function __invoke(Nutgram $bot)
    {
        $this->deleteAllUserData($bot);

        $message = $bot->sendMessage(
            text: '🔄 *Fill all the details below to start bridging\\.*',
            parse_mode: ParseMode::MARKDOWN,
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(InlineKeyboardButton::make('From Token: None', callback_data: 'selectFrom'))
                ->addRow(InlineKeyboardButton::make('To Token: None', callback_data: 'selectTo'))
                ->addRow(InlineKeyboardButton::make('❌ Cancel Bridging', callback_data: 'cancel_all'))
        );

        $bot->setUserData('prev_msg_id', $message->message_id);
    }

    public function deleteAllUserData(Nutgram $bot)
    {
        $bot->deleteUserData('prev_msg_id');
        $bot->deleteUserData('bridge_opt');
        $bot->deleteUserData('action');
        $bot->deleteUserData('from_token');
        $bot->deleteUserData('to_token');
        $bot->deleteUserData('input');
        $bot->deleteUserData('amount');
        $bot->deleteUserData('wallet');
    }
}
