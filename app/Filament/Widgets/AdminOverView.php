<?php

namespace App\Filament\Widgets;

use App\Models\ForexEvent;
use App\Models\Holyday;
use App\Models\Rate;
use App\Models\User;
use App\Models\YearlyTaxCalculation;
use Carbon\Carbon;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class AdminOverView extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Section::make('Config Settings')
                ->schema([
                    Stat::make('Environment', ucfirst(config('app.env')))
                        ->description('Application environment')
                        ->color(config('app.env') === 'production' ? 'success' : 'warning')
                        ->icon(Heroicon::OutlinedServerStack),
                    Stat::make('Queue Driver', ucfirst(config('queue.default')))
                        ->description('Current queue connection')
                        ->color('info')
                        ->icon(Heroicon::OutlinedQueueList),

                    Stat::make('Cache Driver', ucfirst(config('cache.default')))
                        ->description('Active cache driver')
                        ->color('success')
                        ->icon(Heroicon::OutlinedAcademicCap),

                    Stat::make('Debug Mode', config('app.debug') ? 'Enabled' : 'Disabled')
                        ->description('Debug status')
                        ->color(config('app.debug') ? 'danger' : 'success')
                        ->icon(Heroicon::OutlinedBugAnt),
                ])
                ->collapsible()
                ->collapsed(true)
                ->icon(Heroicon::Server)
                ->columns(4)
                ->columnSpanFull(),

            Section::make('Last Fetches and Calculations')
                ->schema([
                    // Data Fetch Status
                    Stat::make('Holidays', $this->getLastStatus(Holyday::class))
                        ->description('Last holiday fetch')
                        ->color($this->getLastStatus(Holyday::class) === 'Not fetched' ? 'danger' : 'success')
                        ->icon(Heroicon::OutlinedCalendarDays),

                    Stat::make('Forex Events', $this->getLastStatus(ForexEvent::class))
                        ->description('Last event fetch')
                        ->color($this->getLastStatus(ForexEvent::class) === 'Not fetched' ? 'danger' : 'success')
                        ->icon(Heroicon::OutlinedChartBar),

                    Stat::make('Exchange Rates', $this->getLastStatus(Rate::class))
                        ->description('Last rate fetch')
                        ->color($this->getLastStatus(Rate::class) === 'Not fetched' ? 'danger' : 'success')
                        ->icon(Heroicon::OutlinedCurrencyDollar),

                    Stat::make('Tax Calculation', $this->getLastStatus(YearlyTaxCalculation::class))
                        ->description('Last tax calculation')
                        ->color($this->getLastStatus(YearlyTaxCalculation::class) === 'Not calculated' ? 'danger' : 'success')
                        ->icon(Heroicon::OutlinedCalculator),
                ])
                ->columns(2)
                ->icon(Heroicon::Calendar)
                ->columns(2)
                ->collapsible()
                ->columnSpanFull(),

            Section::make('Profits')
                ->schema([
                    /*Todo: Implement calculations for all users
                    summary of profits, summary of profits on week, on year, on month
                    and summary on deposits and withdraws, initial balances*/
                    // Reminder: Need to implement stast of withdrawals and deposits on user dashboard too so it should be on a separate widget!
                ])
                ->columns(1)
                ->icon(Heroicon::CurrencyEuro)
                ->collapsible()
                ->columnSpanFull(),

            Section::make('Other Stats')
                ->schema([
                    // User Statistics
                    Stat::make('Registered Users', User::count())
                        ->description('Total users')
                        ->color('success')
                        ->icon(Heroicon::OutlinedUsers),
                ])
                ->columns(1)
                ->icon(Heroicon::DocumentPlus)
                ->collapsible()
                ->columnSpanFull(),
        ];
    }

    private function getLastStatus(string $modelClass): string
    {
        $lastRecord = Cache::remember(
            $modelClass.'_last_created_at',
            Carbon::now()->endOfDay(),
            fn () => $modelClass::query()
                ->orderBy('created_at', 'desc')
                ->first()?->created_at
        );

        if (! $lastRecord) {

            if ($modelClass == YearlyTaxCalculation::class) {
                return 'Not calculated';
            }

            return 'Not fetched';
        }

        return $lastRecord->format('Y-m-d H:i');
    }
}
