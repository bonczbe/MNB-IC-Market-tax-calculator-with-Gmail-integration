<?php

namespace App\Filament\TaxCalculator\Widgets;

use App\Models\BrokerAccount;
use App\Models\User;
use Filament\Support\Colors\Color;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AccountActivityOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Account Activity Overview';

    protected function getStats(): array
    {
        $userId = auth()->id();

        return [
            Stat::make('Total Accounts', $this->getAccountCount($userId))
                ->description('Active broker accounts')
                ->color(Color::Violet)
                ->icon(Heroicon::OutlinedBuildingLibrary),
            Stat::make('Active Currencies', $this->getActiveCurrencyCount($userId))
                ->description('Trading currencies')
                ->color(Color::Fuchsia)
                ->icon(Heroicon::OutlinedGlobeAlt),

        ];
    }

    private function getAccountCount(int $userId): int
    {
        return User::find($userId)?->brokers()?->count() ?? 0;
    }

    private function getActiveCurrencyCount(int $userId): int
    {
        return BrokerAccount::query()->select('broker_currency')->where('user_id', '=', $userId)->distinct()->count();
    }
}
