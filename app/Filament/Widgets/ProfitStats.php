<?php

namespace App\Filament\Widgets;

use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ProfitStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {

        $currentDate = Carbon::now();
        $cards = [];

        $cards = [
            $this->calculateCurrentYearProfit($currentDate),
            $this->calculateCurrentWeekProfit($currentDate),
            $this->calculatecurrentDate($currentDate),
            ...$this->calculatePreviouseYears($currentDate),
        ];

        return $cards;

    }

    private function calculatePreviouseYears($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return $taxService->calculateTaxForPreviouseYears($currentDate);
    }

    private function calculatecurrentDate($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')} Tax",
                Cache::remember('calculatecurrentDate', 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateAllBrokerAccountTaxForActualYear($currentDate);
                })
            );
    }

    private function calculateCurrentWeekProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')}'s {$currentDate->copy()->format('w')} Week Profit",
                Cache::remember('profitForTheWeek', 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateCurrentWeekProfit($currentDate);
                })
            );
    }
    private function calculateCurrentYearProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')}'s Profit",
                Cache::remember('profitForTheWeek', 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateCurrentWeekProfit($currentDate);
                })
            );
    }
}
