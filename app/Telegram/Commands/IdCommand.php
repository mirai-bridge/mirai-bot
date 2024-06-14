<?php

namespace App\Telegram\Commands;

use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\Handlers\Type\Command;
use SergiX44\Nutgram\Telegram\Properties\ParseMode;

class IdCommand extends Command
{
    protected string $command = 'id';

    protected ?string $description = 'Get id chat';

    public function handle(Nutgram $bot): void
    {
        $bot->sendMessage(
            "Your Telegram Unique ID: `{$bot->user()->id}`",
            parse_mode: ParseMode::MARKDOWN
        );
    }
}
