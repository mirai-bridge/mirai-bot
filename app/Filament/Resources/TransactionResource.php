<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Dotenv\Util\Str;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('pairs')
                    ->formatStateUsing(function (string $state, $record) {
                        $pair = explode('_', $state);
                        return $pair[0] . ' > ' . $pair[1];
                    })
                    ->searchable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('amount')
                    ->formatStateUsing(function (string $state, $record) {
                        $pair = explode('_', $record->pairs);
                        return $state . ' ' . $pair[0];
                    }),
                TextColumn::make('output')
                    ->formatStateUsing(function (string $state, $record) {
                        $pair = explode('_', $record->pairs);
                        return $state . ' ' . $pair[1];
                    }),
                TextColumn::make('revenue')->money('USD')->color('success'),
                TextColumn::make('customer.username')
                    ->formatStateUsing(fn (string $state) => '@' . $state),
                TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Done' => 'success',
                        'Failed' => 'danger',
                        'Waiting' => 'warning',
                        'Processing' => 'primary',
                    }),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('id', 'DESC');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            // 'create' => Pages\CreateTransaction::route('/create'),
            // 'edit' => Pages\EditTransaction::route('/{record}/edit'),
        ];
    }
}
