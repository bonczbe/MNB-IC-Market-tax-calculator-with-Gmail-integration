<?php

namespace App\Filament\Widgets;

use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ProfitStats extends StatsOverviewWidget
{
    protected function getColumns(): int
{
    return 4;
}

    protected function getStats(): array
    {

        $currentDate = Carbon::now();
        $cards = [];

        $cards = [
            $this->calculateCurrentWeekNetProfit($currentDate),
            $this->calculateGrossProfit($currentDate),
            $this->calculateCurrentYearProfit($currentDate),
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
            )->columnSpan(2);
    }

    private function calculateCurrentWeekNetProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')}'s {$currentDate->copy()->format('w')}'s Week Net Profit",
                Cache::remember('profitForTheWeek', 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateCurrentWeekNetProfit($currentDate);
                })
            )->columnSpan(2);;
    }

    private function calculateGrossProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')}'s Gross Profit",
                Cache::remember('grossProfitOfYear', 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateGrossProfitOfYear($currentDate);
                })
            )->columnSpan(2);;
    }

    private function calculateCurrentYearProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')}'s Net Profit",
                Cache::remember('profitForYear', 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateCurrentYearProfit($currentDate);
                })
            )->columnSpan(2);;
    }
}
