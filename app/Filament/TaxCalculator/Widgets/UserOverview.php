<?php

namespace App\Filament\TaxCalculator\Widgets;

use App\Enums\CalculationIntervalEnum;
use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class UserOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'User Overview';

    protected function getStats(): array
    {
        $currentDate = Carbon::now();
        $taxService = app(TaxCalculatorService::class);

        $netProfit = Cache::remember('profitForWeek'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
            return $taxService->calculateCurrentNetProfit($currentDate, auth()->user()->id, CalculationIntervalEnum::WEEK);
        });

        $profitForMoth = Cache::remember('profitForMonth'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
            return $taxService->calculateCurrentNetProfit($currentDate, auth()->user()->id, CalculationIntervalEnum::MONTH);
        });

        $profitForYear = Cache::remember('profitForYear'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
            return $taxService->calculateCurrentNetProfit($currentDate, auth()->user()->id, CalculationIntervalEnum::YEAR);
        });

        return [
            Stat::make(
                'Week Profit',
                $netProfit
            )
                ->columnSpan(1)
                ->description('After-tax profit this week')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color(($netProfit < 0) ? Color::Red : (($netProfit > 0) ? Color::Green : Color::Amber)),
            Stat::make(
                "{$currentDate->format('M')} Profit",
                $profitForMoth
            )
                ->description('After-tax profit this moth')
                ->descriptionIcon(Heroicon::OutlinedCalendarDateRange)
                ->color(($profitForMoth < 0) ? Color::Red : (($profitForMoth > 0) ? Color::Green : Color::Amber)),
            Stat::make(
                $currentDate->format('Y').' Profit',
                $profitForYear
            )
                ->description('After-tax profit this year')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color(($profitForYear < 0) ? Color::Red : (($profitForYear > 0) ? Color::Green : Color::Amber)),

        ];
    }
}
