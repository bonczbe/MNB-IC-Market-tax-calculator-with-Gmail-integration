<?php

namespace App\Filament\Widgets;

use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
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
            $this->calculateCurrentYearNetProfit($currentDate),
            $this->calculatecurrentYearTax($currentDate),
            ...$this->calculatePreviouseYears($currentDate),
        ];

        return $cards;

    }

    private function calculatePreviouseYears($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return $taxService->calculateTaxForpreviousYears($currentDate);
    }

    private function calculatecurrentYearTax($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')} Tax Due",
                Cache::remember('calculatecurrentDate'.auth()->user()->id, 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateAllBrokerAccountTaxForActualYear($currentDate);
                })
            )->columnSpan(2)->description('Estimated tax this year')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color(Color::Amber);
    }

    private function calculateCurrentWeekNetProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "Week {$currentDate->format('W')} Net Profit",
                Cache::remember('profitForTheWeek'.auth()->user()->id.'w_'.$currentDate->format('W'), 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateCurrentWeekNetProfit($currentDate);
                })
            )->columnSpan(2)
                ->description('After-tax profit this week')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color(Color::Lime);
    }

    private function calculateGrossProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->format('Y')} Gross Profit",
                Cache::remember('grossProfitOfYear'.auth()->user()->id, 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateGrossProfitOfYear($currentDate);
                })
            )->columnSpan(2)->description('Before tax deductions')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color(Color::Sky);
    }

    private function calculateCurrentYearNetProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->format('Y')} Net Profit",
                Cache::remember('profitForYear'.auth()->user()->id, 3600, function () use ($taxService, $currentDate) {
                    return $taxService->calculateCurrentYearNetProfit($currentDate);
                })
            )->columnSpan(2)->description('After-tax profit this year')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success');
    }
}
