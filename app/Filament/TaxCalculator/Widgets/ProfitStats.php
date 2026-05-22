<?php

namespace App\Filament\TaxCalculator\Widgets;

use App\Enums\CalculationIntervalEnum;
use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Schemas\Components\Section;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ProfitStats extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {

        $currentDate = Carbon::now();
        $cards = [];

        $cards = [
            Section::make('Tax Stats')
                ->icon(Heroicon::OutlinedBookOpen)
                ->schema([
                    $this->calculateGrossProfitOfYear($currentDate),
                    $this->calculateCurrentYearNetProfit($currentDate),
                    $this->calculatecurrentYearTax($currentDate),
                ])
                ->collapsible()
                ->columnSpanFull()
                ->columns(3),
        ];

        return $cards;

    }

    private function calculatecurrentYearTax($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->copy()->format('Y')} Tax Due",
                Cache::remember('calculatecurrentDate'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
                    return $taxService->calculateAllBrokerAccountTaxForActualYear($currentDate, auth()->user()->id);
                })
            )
                ->columnSpan(1)
                ->description('Estimated tax this year')
                ->descriptionIcon('heroicon-m-receipt-percent')
                ->color(Color::Amber);
    }

    private function calculateCurrentYearNetProfit($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentDate->format('Y')} Net Profit",
                Cache::remember('profitForYear'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
                    return $taxService->calculateCurrentNetProfit($currentDate, auth()->user()->id, CalculationIntervalEnum::YEAR);
                })
            )
                ->columnSpan(1)
                ->description('After-tax profit this year')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success');
    }

    /*

     */
    private function calculateGrossProfitOfYear($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return
                    Stat::make(
                        "{$currentDate->format('Y')} Gross Profit",
                        Cache::remember('grossProfitOfYear'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
                            return $taxService->calculateGrossProfitOfYear($currentDate, auth()->user()->id);
                        })
                    )
                        ->description('Before tax deductions')
                        ->descriptionIcon('heroicon-m-arrow-trending-up')
                        ->color(Color::Amber);
    }
}
