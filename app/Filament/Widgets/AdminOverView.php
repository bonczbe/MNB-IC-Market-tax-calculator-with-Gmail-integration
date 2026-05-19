<?php

namespace App\Filament\Widgets;

use App\Models\ForexEvent;
use App\Models\Rate;
use App\Models\YearlyTaxCalculation;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Colors\Color;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class AdminOverView extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Section::make('')->schema([
                Action::make('clearCache')
                    ->label('Clear All Caches')
                    ->icon('heroicon-o-trash')
                    ->color(Color::Green)
                    ->action(function () {
                        try {
                            Cache::flush();
                            Notification::make()
                                ->title('Success')
                                ->body('Cache cleared successfully.')
                                ->success()
                                ->persistent()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error')
                                ->body('Something went wrong!')
                                ->warning()
                                ->persistent()
                                ->send();
                        }
                    }),
            ])->columnSpanFull(),
            Section::make([
                Stat::make('APP_ENV', config('app.env'))
                    ->description('Active environment')
                    ->color('gray'),

                Stat::make('Queue driver', config('queue.default'))
                    ->description('Current queue connection')
                    ->color('info'),

                Stat::make('Cache driver', config('cache.default'))
                    ->description('Active cache driver')
                    ->color('success'),

                Stat::make('Debug', config('app.debug') ? 'ON' : 'OFF')
                    ->description('Debug mode')
                    ->color(config('app.debug') ? 'danger' : 'success'),

                Stat::make('Get Holidays', $this->getLast(Rate::class, 'No holiday fetched yet'))
                    ->description('Last Holiday fetch')
                    ->color(config('app.debug') ? 'danger' : 'success'),

                Stat::make('Get events', $this->getLast(ForexEvent::class, 'No event fetched yet'))
                    ->description('Last event fetch')
                    ->color(config('app.debug') ? 'danger' : 'success'),

                Stat::make('Get rates', $this->getLast(Rate::class, 'No rate fetched yet'))
                    ->description('Last Rate fetch')
                    ->color(config('app.debug') ? 'danger' : 'success'),

                Stat::make('Year Tax Calculation', $this->getLast(YearlyTaxCalculation::class, 'No calculated yearly tax yet'))
                    ->description('Is previouse year tax calculated')
                    ->color(config('app.debug') ? 'danger' : 'success'),

            ])->columnSpanFull()->columns(4),

        ];
    }

    private function getLast(string $modelClass, string $ifNullResponse)
    {
        return Cache::remember(
            $modelClass.'_'.'newest_created_at',
            Carbon::now()->endOfDay(),
            fn () => $modelClass::query()
                ->orderBy('created_at', 'desc')
                ->first()?->created_at->format('Y-m-d H:i') ?? $ifNullResponse
        );
    }
}
