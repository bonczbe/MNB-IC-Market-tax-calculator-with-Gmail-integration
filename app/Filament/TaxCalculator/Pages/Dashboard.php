<?php

namespace App\Filament\TaxCalculator\Pages;

use App\Filament\TaxCalculator\Widgets\AccountActivityOverview;
use App\Filament\TaxCalculator\Widgets\PrevProfitStats;
use App\Filament\TaxCalculator\Widgets\ProfitStats;
use App\Filament\TaxCalculator\Widgets\UserOverview;
use App\Filament\TaxCalculator\Widgets\Weekly;
use App\Filament\TaxCalculator\Widgets\Yearly;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            ProfitStats::class,
            UserOverview::class,
            AccountActivityOverview::class,
            Weekly::class,
            Yearly::class,
            PrevProfitStats::class,
        ];
    }
}
