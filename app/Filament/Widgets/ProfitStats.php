<?php

namespace App\Filament\Widgets;

use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Cache;

class ProfitStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {

        $currentYear = Carbon::now();
        $cards = [];

        $cards = [
            $this->calculateCurrentYear($currentYear),
            ...$this->calculatePreviouseYears($currentYear),
        ];

        return $cards;

    }

    private function calculatePreviouseYears($currentYear)
    {
        $taxService = app(TaxCalculatorService::class);

        return $taxService->calculateTaxForPreviouseYears($currentYear);
    }

    private function calculateCurrentYear($currentYear)
    {
        $taxService = app(TaxCalculatorService::class);

        return
            Stat::make(
                "{$currentYear->copy()->format('Y')} Tax",
                Cache::remember('calculateCurrentYear', 3600, function () use ($taxService, $currentYear) {
                    return $taxService->calculateAllBrokerAccountTaxForActualYear($currentYear);
                })
            );
    }
}
