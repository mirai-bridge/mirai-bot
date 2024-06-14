<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class AdminOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $cus = Trend::model(Customer::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        $tx = Trend::model(Transaction::class)
            ->between(
                start: now()->subYear(),
                end: now(),
            )
            ->perMonth()
            ->count();

        $revenue = number_format(Transaction::sum('revenue'));

        return [
            Stat::make('Total Customer', Customer::count())
                ->chart($cus->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('info'),
            Stat::make('Total Transaction', Transaction::count())
                ->chart($tx->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('success'),
            Stat::make('Total Revenue', '$' . $revenue)
                ->chart($tx->map(fn (TrendValue $value) => $value->aggregate)->toArray())
                ->color('success')
        ];
    }
}
