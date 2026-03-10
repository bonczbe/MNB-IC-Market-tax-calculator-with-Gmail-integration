<?php

namespace App\Filament\Widgets;

use App\Models\Rate;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ProfitStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $previousYear = Carbon::now()
        ->subYear();
        $currentYear = Carbon::now();

        return [
            Stat::make(
                "{$currentYear->copy()->format('Y')} Tax",
                function () use ($currentYear) {
                    $startOfYear = $currentYear->copy()->startOfYear();
                    $endOfYear = $currentYear->copy()->endOfYear();

                    $rates = Rate::query()
                        ->whereBetween('date', [$startOfYear, $endOfYear])
                        ->get(['rate', 'date', 'base_currency']);

                    return env('BASE_CURRENCY', 'HUF');
                }
            ),
            Stat::make(
                "{$previousYear->copy()->format('Y')} Tax",
                function () use ($previousYear) {
                    $startOfYear = $previousYear->copy()->startOfYear();
                    $endOfYear = $previousYear->copy()->endOfYear();

                    $rates = Rate::query()
                        ->whereBetween('date', [$startOfYear, $endOfYear])
                        ->get(['rate', 'date', 'base_currency']);

                    return env('BASE_CURRENCY', 'HUF');
                }
            ),
        ];
    }
}
