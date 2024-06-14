<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords\Tab;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Waiting' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->where('status', 'Waiting');
            }),
            'Processing' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->where('status', 'Processing');
            }),
            'Done' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->where('status', 'Done');
            }),
            'Failed' => Tab::make()->modifyQueryUsing(function ($query) {
                $query->where('status', 'Failed');
            }),
        ];
    }
}
