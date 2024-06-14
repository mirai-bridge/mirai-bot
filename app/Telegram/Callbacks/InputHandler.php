<?php

namespace App\Telegram\Callbacks;

use App\Models\Network;
use App\Models\Ticker;
use App\Models\Transaction;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class InputHandler
{
    public function __invoke(Nutgram $bot)
    {
        $data = $bot->message()->text;
        $input = $bot->getUserData('input');

        if (str_contains($data, "/order_")) {
            $id = explode('_', $data)[1];
            $tx = Transaction::find($id);
            $hash = $tx->hash ? $tx->hash->hash : '-';

            if (!$tx) {
                $bot->sendMessage('Order ID not valid');
                return;
            }

            if ($tx->customer->telegram_id != $bot->user()->id) {
                return;
            }

            $bot->setUserData('prev_tx', $tx->id);

            $pair = explode('_', $tx->pairs);
            $from = $pair[0];
            $to = $pair[1];
            $deposit = $tx->dispenser?->address;

            // For ripple deposit
            if ($from === 'XRP') {
                $deposit = Network::find(5)->wallet->address;
            }

            // For ERC20 token deposit
            $tokens = ['PEPE', 'SHIB', 'USDT'];
            if (in_array($from, $tokens)) {
                $deposit = Network::find(2)->wallet->address;
            }

            // For BUSD deposit
            if ($from === 'BUSD') {
                $deposit = Network::find(1)->wallet->address;
            }

            $xrp_note = $from == 'XRP' ? "\n<b>Destination Tag/MEMO:</b> <code>{$tx->id}</code>" : '';

            $text = <<<EOD
            <b>Bridging ID:</b> <code>$tx->id</code> <b>— Bridging Transaction started:</b>
        
            • Send: $tx->amount $from
            • Receive: $tx->output $to
            • Receive Wallet: <code>$tx->receiver</code>
    
            ➡️ <b>Please transfer $tx->amount $from to start bridging process.</b> 
            <b>Deposit Wallet:</b> 
            <code>$deposit</code>$xrp_note

            <b>Hash:</b> <code>$hash</code>
    
            <b>You will get $tx->output $to, for your $tx->amount $from</b>

            <b>Note:</b> <i>You must complete this trade within a maximum of 30 minutes. Fees can vary between</i> <b>0.1%</b> and <b>0.5%</b>.

            <b>📡 Status: $tx->status</b>
            EOD;

            $msg = $bot->sendMessage($text, parse_mode: ParseMode::HTML, reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('🔎 Refresh Status', callback_data: 'refStatus'),
                    InlineKeyboardButton::make('Delete Order', callback_data: 'deleteOrder')
                ));

            $bot->setUserData('prev_msg_id', $msg->message_id);
            return;
        }

        $bot->deleteMessage($bot->message()->from->id, $bot->message()->message_id);
        $bot->deleteMessage($bot->message()->from->id, $bot->getUserData('ask_msg_id'));

        if ($input && $input === 'amount') {
            $bot->setUserData('amount', $data);
        }

        if ($input && $input === 'wallet') {
            $bot->setUserData('wallet', $data);
        }

        $from = $bot->getUserData('from_token');
        $to = $bot->getUserData('to_token');
        $amount = $bot->getUserData('amount') ?? 'None';
        $wallet = $bot->getUserData('wallet') ?? 'None';

        $fromTicker = $from ? Ticker::find($from)->name : 'None';
        $toTicker = $to ? Ticker::find($to)->name : 'None';

        if ($amount !== 'None' && $wallet !== 'None') {
            $bot->editMessageText(
                chat_id: $bot->message()->from->id,
                message_id: $bot->getUserData('prev_msg_id'),
                text: '🔄 *Fill all the details below to start bridging\\.*',
                parse_mode: ParseMode::MARKDOWN,
                reply_markup: InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('From Token: ' . $fromTicker, callback_data: 'selectFrom'))
                    ->addRow(InlineKeyboardButton::make('To Token: ' . $toTicker, callback_data: 'selectTo'))
                    ->addRow(InlineKeyboardButton::make('Amount: ' . $amount, callback_data: 'enterAmount'))
                    ->addRow(InlineKeyboardButton::make('Receive Wallet: ' . $wallet, callback_data: 'enterWallet'))
                    ->addRow(
                        InlineKeyboardButton::make('🔄 Bridge', callback_data: 'bridge_now'),
                        InlineKeyboardButton::make('❌ Cancel Bridging', callback_data: 'cancel_all')
                    )
            );
        } else {
            $bot->editMessageText(
                chat_id: $bot->message()->from->id,
                message_id: $bot->getUserData('prev_msg_id'),
                text: '🔄 *Fill all the details below to start bridging\\.*',
                parse_mode: ParseMode::MARKDOWN,
                reply_markup: InlineKeyboardMarkup::make()
                    ->addRow(InlineKeyboardButton::make('From Token: ' . $fromTicker, callback_data: 'selectFrom'))
                    ->addRow(InlineKeyboardButton::make('To Token: ' . $toTicker, callback_data: 'selectTo'))
                    ->addRow(InlineKeyboardButton::make('Amount: ' . $amount, callback_data: 'enterAmount'))
                    ->addRow(InlineKeyboardButton::make('Receive Wallet: ' . $wallet, callback_data: 'enterWallet'))
                    ->addRow(InlineKeyboardButton::make('❌ Cancel Bridging', callback_data: 'cancel_all'))
            );
        }
    }
}
