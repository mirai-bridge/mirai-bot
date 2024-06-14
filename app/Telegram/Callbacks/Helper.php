<?php

namespace App\Telegram\Callbacks;

use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardButton;
use SergiX44\Nutgram\Telegram\Types\Keyboard\InlineKeyboardMarkup;

trait Helper
{
    public static function buildTickerButtons($value)
    {
        $inKeyboard = InlineKeyboardMarkup::make();

        $tot = ceil(count($value) / 2);
        for ($i = 0; $i < $tot; $i++) {
            $m = $i * 2;

            if (empty($value[$m + 1])) {
                $inKeyboard->addRow(
                    InlineKeyboardButton::make($value[$m]->name, callback_data: 'opt_' . $value[$m]->id)
                );
            } else {
                $inKeyboard->addRow(
                    InlineKeyboardButton::make($value[$m]->name, callback_data: 'opt_' . $value[$m]->id),
                    InlineKeyboardButton::make($value[$m + 1]->name, callback_data: 'opt_' . $value[$m + 1]->id)
                );
            }
        }

        return $inKeyboard;
    }
}
