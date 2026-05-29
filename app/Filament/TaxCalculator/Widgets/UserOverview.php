<?php

namespace App\Filament\TaxCalculator\Widgets;

use App\Enums\CalculationIntervalEnum;
use App\Models\BrokerAccount;
use App\Models\User;
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
            Section::make('Financial Overview')
                ->description('Your current financial snapshot')
                ->icon(Heroicon::OutlinedChartBar)
                ->schema([
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
                ])
                ->collapsed()
                ->columnSpanFull()
                ->columns(2),
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
}
