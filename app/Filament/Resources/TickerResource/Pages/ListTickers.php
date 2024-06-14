<?php

namespace App\Filament\Resources\TickerResource\Pages;

use App\Filament\Resources\TickerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTickers extends ListRecords
{
    protected static string $resource = TickerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
