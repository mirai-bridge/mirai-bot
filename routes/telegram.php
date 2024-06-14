<?php

/** @var SergiX44\Nutgram\Nutgram $bot */

use App\Models\Transaction;
use App\Telegram\Callbacks\Cancel;
use App\Telegram\Callbacks\EnterAmount;
use App\Telegram\Callbacks\EnterWallet;
use App\Telegram\Callbacks\InputHandler;
use App\Telegram\Callbacks\ProcessBridge;
use App\Telegram\Callbacks\SelectNetwork;
use App\Telegram\Callbacks\StartBridge;
use App\Telegram\Callbacks\TickerHandler;
use App\Telegram\Commands\History;
use App\Telegram\Commands\HistoryCommand;
use App\Telegram\Commands\IdCommand;
use App\Telegram\Commands\RefreshCommand;
use App\Telegram\Commands\StartCommand;
use Nutgram\Laravel\Facades\Telegram;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Properties\UpdateType;

/*
|--------------------------------------------------------------------------
| Nutgram Handlers
|--------------------------------------------------------------------------
|
| Here is where you can register telegram handlers for Nutgram. These
| handlers are loaded by the NutgramServiceProvider. Enjoy!
|
*/

Telegram::registerCommand(StartCommand::class);
Telegram::onCommand('new', StartBridge::class);

/**
 * Bridge action
 */
Telegram::onCallbackQueryData('bridge', StartBridge::class);
Telegram::onCallbackQueryData('selectFrom|selectTo', SelectNetwork::class);
Telegram::fallbackOn(UpdateType::CALLBACK_QUERY, TickerHandler::class);
Telegram::onCallbackQueryData('enterAmount', EnterAmount::class);
Telegram::onCallbackQueryData('enterWallet', EnterWallet::class);
Telegram::fallbackOn(UpdateType::MESSAGE, InputHandler::class);
Telegram::onCallbackQueryData('refStatus', RefreshCommand::class);

// Cancel bridge
Telegram::onCallbackQueryData('cancel_all', Cancel::class);
Telegram::onCommand('cancel', Cancel::class);
Telegram::onCallbackQueryData('deleteOrder', function (Nutgram $bot) {
    $tx = Transaction::find($bot->getUserData('prev_tx'))->delete();

    if ($tx) {
        $bot->deleteMessage(chat_id: $bot->user()->id, message_id: $bot->getUserData('prev_msg_id'));
        $bot->sendMessage('<b>Order deleted</b>', parse_mode: ParseMode::HTML);
    }
});

// Bridge process
Telegram::onCallbackQueryData('bridge_now', ProcessBridge::class);

// History command
Telegram::onCommand('history', History::class);
Telegram::onCallbackQueryData('history', History::class);

Telegram::registerCommand(IdCommand::class);
