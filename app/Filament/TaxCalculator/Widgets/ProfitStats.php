<?php

namespace App\Filament\TaxCalculator\Widgets;

use App\Enums\CalculationIntervalEnum;
use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ProfitStats extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Tax Stats Overview';

    protected function getStats(): array
    {

        $currentDate = Carbon::now();
        $cards = [];

        $cards = [
            $this->calculateGrossProfitOfYear($currentDate),
            $this->calculateCurrentYearNetProfit($currentDate),
            $this->calculatecurrentYearTax($currentDate),
        ];

        return $cards;

    }

    private function calculatecurrentYearTax($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        $profit = Cache::remember('calculatecurrentDate'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
            return $taxService->calculateAllBrokerAccountTaxForActualYear($currentDate, auth()->user()->id);
        });

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')} Tax Due",
                $profit
            )
                ->columnSpan(1)
                ->description('Estimated tax this year')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color(($profit < 0) ? Color::Red : (($profit > 0) ? Color::Green : Color::Amber));
    }

    private function calculateCurrentYearNetProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        $profit = Cache::remember('profitForYear'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
            return $taxService->calculateCurrentNetProfit($currentDate, auth()->user()->id, CalculationIntervalEnum::YEAR);
        });

        return
            Stat::make(
                "{$currentDate->format('Y')} Net Profit",
                $profit
            )
                ->columnSpan(1)
                ->description('After-tax profit this year')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color(($profit < 0) ? Color::Red : (($profit > 0) ? Color::Green : Color::Amber));
    }

    /*

     */
    private function calculateGrossProfitOfYear($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        $profit = Cache::remember('grossProfitOfYear'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
            return $taxService->calculateGrossProfitOfYear($currentDate, auth()->user()->id);
        });

        return
                    Stat::make(
                        "{$currentDate->format('Y')} Gross Profit",
                        $profit
                    )
                        ->description('Before tax deductions')
                        ->descriptionIcon('heroicon-m-arrow-trending-up')
                        ->color(($profit < 0) ? Color::Red : (($profit > 0) ? Color::Green : Color::Amber));
    }
}
