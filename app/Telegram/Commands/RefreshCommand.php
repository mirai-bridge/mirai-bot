<?php

namespace App\Telegram\Commands;

use App\Models\Network;
use App\Models\Transaction;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class RefreshCommand
{
    public function __invoke(Nutgram $bot): void
    {
        $tx = Transaction::find($bot->getUserData('prev_tx'));
        $hash = $tx->hash ? $tx->hash->hash : '-';

        if (!$tx) {
            $bot->sendMessage('Order ID not valid');
            return;
        }

        if ($tx->customer->telegram_id != $bot->user()->id) {
            return;
        }

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

        $bot->editMessageText(
            chat_id: $bot->callbackQuery()->from->id,
            message_id: $bot->getUserData('prev_msg_id'),
            text: $text,
            parse_mode: ParseMode::HTML,
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('🔎 Refresh Status', callback_data: 'refStatus'),
                    InlineKeyboardButton::make('Delete Order', callback_data: 'deleteOrder')
                )
        );
    }
}
