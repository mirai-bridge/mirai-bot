<?php

namespace App\Telegram\Commands;

use App\Models\Customer;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

class StartCommand extends Command
{
    protected string $command = 'start';

    protected ?string $description = 'Start here!';

    public function handle(Nutgram $bot): void
    {
        $this->registerUser($bot, $bot->message()->from);

        $text = <<<EOD
        <b>Mirai Bridge</b>
        
        Welcome to Mirai Bridge! 🚀 Our bot helps you effortlessly bridge your crypto assets across multiple networks, including ETH, BNB, XRP, Arbitrum, TON, and SOL. Start trading or swapping securely and efficiently with us. Let’s make your crypto transactions seamless!
        EOD;

        $bot->sendMessage(
            $text,
            parse_mode: ParseMode::HTML,
            reply_markup: InlineKeyboardMarkup::make()
                ->addRow(
                    InlineKeyboardButton::make('🔁 Start Bridge', callback_data: 'bridge'),
                    InlineKeyboardButton::make('📃 History', callback_data: 'history')
                )
                ->addRow(
                    InlineKeyboardButton::make('📰 Whitepapper', url: 'https://bots8.com/assets/Miracle-Bridge-Whitepaper.pdf'),
                    InlineKeyboardButton::make('💬 Telegram', url: 'https://t.me')
                )
                ->addRow(
                    InlineKeyboardButton::make('🌐 Website', url: 'https://example.com'),
                    InlineKeyboardButton::make('𝕏 Twitter', url: 'https://x.com/Mirai_Bridge_')
                )
        );
    }

    public function registerUser($bot, $from)
    {
        $user = Customer::where('telegram_id', $from->id)->exists();

        if ($user) return;

        $profilePicture = $bot->getUserProfilePhotos($bot->user()->id);

        if ($profilePicture->photos && count($profilePicture->photos) > 0) {
            $fileId = $profilePicture->photos[0][0]->file_id;
            $path = 'storage/avatars/' . $from->id . '.png';
            $bot->getFile($fileId)->save(public_path($path));
        }

        Customer::create([
            'name' => $from->first_name . ' ' . $from->last_name,
            'username' => $from->username,
            'telegram_id' => $from->id,
            'language' => $from->language_code,
            'avatar' => 'avatars/' . $from->id . '.png' ?? 'default.png'
        ]);
    }
}
