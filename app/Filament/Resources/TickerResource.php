<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TickerResource\Pages;
use App\Filament\Resources\TickerResource\RelationManagers;
use App\Models\Ticker;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TickerResource extends Resource
{
    protected static ?string $model = Ticker::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name'),
                TextInput::make('symbol'),
                TextInput::make('minimum')->numeric(),
                TextInput::make('usd_price')->numeric(),
                Select::make('status')->options([
                    'Active' => 'Active',
                    'Disabled' => 'Disabled',
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('symbol')
                    ->searchable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('minimum')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('usd_price')
                    ->sortable()
                    ->label('Price')
                    ->money('USD'),
                TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'success',
                        'Disabled' => 'danger',
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickers::route('/'),
            // 'create' => Pages\CreateTicker::route('/create'),
            'edit' => Pages\EditTicker::route('/{record}/edit'),
        ];
    }
}
