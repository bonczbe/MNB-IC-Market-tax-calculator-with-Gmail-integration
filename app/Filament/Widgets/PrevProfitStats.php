<?php

namespace App\Filament\Widgets;

use App\Services\TaxCalculatorService;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;

class PrevProfitStats extends StatsOverviewWidget
{
    protected function getColumns(): int
    {
        return 4;
    }

    protected function getStats(): array
    {

        $currentDate = Carbon::now();
        $cards = [];

        $cards = [
            ...$this->calculatepreviousYears($currentDate),
        ];

        return $cards;

    }

    private function calculatepreviousYears($currentDate)
    {
        $taxService = app(TaxCalculatorService::class);

        return $taxService->calculateTaxForPreviousYears($currentDate);
    }
}
