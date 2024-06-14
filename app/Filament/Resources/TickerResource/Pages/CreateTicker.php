<?php

namespace App\Filament\Resources\TickerResource\Pages;

use App\Filament\Resources\TickerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTicker extends CreateRecord
{
    protected static string $resource = TickerResource::class;
}
