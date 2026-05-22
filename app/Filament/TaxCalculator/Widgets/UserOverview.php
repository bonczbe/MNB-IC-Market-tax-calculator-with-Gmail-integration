<?php

namespace App\Filament\TaxCalculator\Widgets;

use App\Models\BrokerAccount;
use App\Models\User;
use App\Models\YearlyTaxCalculation;
use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Schemas\Components\Section;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class UserOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $currentDate = Carbon::now();
        $taxService = app(TaxCalculatorService::class);
        $userId = auth()->id();

        return [
            Section::make('Financial Overview')
                ->description('Your current financial snapshot')
                ->icon(Heroicon::OutlinedChartBar)
                ->schema([
                    Stat::make(
                        'Week Profit',
                        Cache::remember('profitForTheWeek'.auth()->user()->id.'w_'.$currentDate->format('W'), Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
                            return $taxService->calculateCurrentWeekNetProfit($currentDate, auth()->user()->id);
                        })
                    )
                        ->columnSpan(1)
                        ->description('After-tax profit this week')
                        ->descriptionIcon('heroicon-m-calendar-days')
                        ->color(Color::Sky),
                    Stat::make(
                        "{$currentDate->format('M')} Profit",
                        Cache::remember('profitForTheMonth'.auth()->user()->id.'w_'.$currentDate->format('W'), Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
                            return $taxService->calculateCurrentMonthNetProfit($currentDate, auth()->user()->id);
                        })
                    )
                        ->description('After-tax profit this moth')
                        ->descriptionIcon(Heroicon::OutlinedCalendarDateRange)
                        ->color(Color::Sky),
                    Stat::make(
                        $currentDate->format('Y').' Profit',
                        Cache::remember('profitForTheYear'.auth()->user()->id.'w_'.$currentDate->format('W'), Carbon::now()->endOfDay()->subMinute(1), function () use ($taxService, $currentDate) {
                            return $taxService->calculateCurrentYearNetProfit($currentDate, auth()->user()->id);
                        })
                    )
                        ->description('After-tax profit this year')
                        ->descriptionIcon('heroicon-m-calendar-days')
                        ->color(Color::Sky),
                ])
                ->collapsible()
                ->columnSpanFull()
                ->columns(3),
            Section::make('Account Activity')
                ->description('Recent account movements')
                ->icon(Heroicon::OutlinedArrowsRightLeft)
                ->schema([
                    Stat::make('Total Accounts', $this->getAccountCount($userId))
                        ->description('Active broker accounts')
                        ->color(Color::Violet)
                        ->icon(Heroicon::OutlinedBuildingLibrary),
                    Stat::make('Active Currencies', $this->getActiveCurrencyCount($userId))
                        ->description('Trading currencies')
                        ->color(Color::Fuchsia)
                        ->icon(Heroicon::OutlinedGlobeAlt),
                    Stat::make('Last Tax Year ( '.$currentDate->subYear()->format('Y').' )', $this->getLastTaxYear($userId, $currentDate))
                        ->description('Most recent calculation')
                        ->color(Color::Teal)
                        ->icon(Heroicon::OutlinedCalendarDays),
                ])
                ->collapsed()
                ->columnSpanFull()
                ->columns(3),
        ];
    }

    private function getAccountCount(int $userId): int
    {
        return User::find($userId)?->brokers()?->count() ?? 0;
    }

    private function getActiveCurrencyCount(int $userId): int
    {
        return BrokerAccount::where('user_id', '=', $userId)->distinct()->pluck('broker_currency')->count();
    }

    private function getLastTaxYear(int $userId, Carbon $currentDate): string
    {
        return YearlyTaxCalculation::whereHas('broker', function ($query) use ($userId, $currentDate) {
            return $query->where('user_id', $userId)->where('tax_year', $currentDate->subYear()->format('Y'));
        }
        )->sum('tax_amount');
    }
}
