<?php

namespace App\Filament\Resources\TickerResource\Pages;

use App\Filament\Resources\TickerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTicker extends EditRecord
{
    protected static string $resource = TickerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
