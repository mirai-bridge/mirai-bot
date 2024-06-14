<?php

namespace App\Telegram\Callbacks;

use App\Models\Network;
use App\Models\Ticker;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class TickerHandler
{
    use Helper;

    public function __invoke(Nutgram $bot)
    {
        $data = $bot->callbackQuery()->data;

        if (!str_contains($data, 'opt')) {
            $bot->answerCallbackQuery();
            return;
        }

        $action = $bot->getUserData('action');

        if ($action && $action === 'select_network') {
            $opt = explode('_', $data);
            $ticker = Network::find($opt[1])->tickers()->where('status', 'Active')->get();

            $bot->editMessageText(
                chat_id: $bot->callbackQuery()->from->id,
                message_id: $bot->getUserData('prev_msg_id'),
                text: '🔼 *Choose the token you want to deposit*',
                parse_mode: ParseMode::MARKDOWN,
                reply_markup: $this->buildTickerButtons($ticker)
                    ->addRow(InlineKeyboardButton::make('❌ Cancel Bridging', callback_data: 'cancel_all'))
            );

            $bot->setUserData('action', 'prep_bridge');
        }

        $bridge_opt = $bot->getUserData('bridge_opt');

        if ($bridge_opt && $bridge_opt === 'selectFrom') {
            $opt = explode('_', $data);
            $bot->setUserData('from_token', $opt[1]);
        }

        if ($bridge_opt && $bridge_opt === 'selectTo') {
            $opt = explode('_', $data);
            $bot->setUserData('to_token', $opt[1]);
        }

        if ($action && $action === 'prep_bridge') {
            $from = $bot->getUserData('from_token');
            $to = $bot->getUserData('to_token');
            $fromTicker = $from ? Ticker::find($from)->name : 'None';
            $toTicker = $to ? Ticker::find($to)->name : 'None';

            if ($fromTicker && $toTicker) {
                $bot->editMessageText(
                    chat_id: $bot->callbackQuery()->from->id,
                    message_id: $bot->getUserData('prev_msg_id'),
                    text: '🔄 *Fill all the details below to start bridging\\.*',
                    parse_mode: ParseMode::MARKDOWN,
                    reply_markup: InlineKeyboardMarkup::make()
                        ->addRow(InlineKeyboardButton::make('From Token: ' . $fromTicker, callback_data: 'selectFrom'))
                        ->addRow(InlineKeyboardButton::make('To Token: ' . $toTicker, callback_data: 'selectTo'))
                        ->addRow(InlineKeyboardButton::make('Amount: None', callback_data: 'enterAmount'))
                        ->addRow(InlineKeyboardButton::make('Receive Wallet: None', callback_data: 'enterWallet'))
                        ->addRow(InlineKeyboardButton::make('❌ Cancel Bridging', callback_data: 'cancel_all'))
                );
            } else {
                $bot->editMessageText(
                    chat_id: $bot->callbackQuery()->from->id,
                    message_id: $bot->getUserData('prev_msg_id'),
                    text: '🔄 *Fill all the details below to start bridging\\.*',
                    parse_mode: ParseMode::MARKDOWN,
                    reply_markup: InlineKeyboardMarkup::make()
                        ->addRow(InlineKeyboardButton::make('From Token: ' . $fromTicker, callback_data: 'selectFrom'))
                        ->addRow(InlineKeyboardButton::make('To Token: ' . $toTicker, callback_data: 'selectTo'))
                        ->addRow(InlineKeyboardButton::make('❌ Cancel Bridging', callback_data: 'cancel_all'))
                );
            }
        }
    }
}
