<?php

namespace App\Filament\Widgets;

use App\Services\ChartService;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class Yearly extends ChartWidget
{
    protected ?string $heading = 'Yearly Movements';

    public ?string $filter = 'current_year';

    protected bool $isCollapsible = true;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $activeYear = $this->filter;

        $chartService = app(ChartService::class);

        $data = Cache::remember('yearly_chart_data_'.auth()->user()->id.'_'.$activeYear, Carbon::now()->endOfDay()->subMinute(1), function () use ($chartService, $activeYear) {
            return $chartService->getYearlyChartData($activeYear);
        });

        return [
            'datasets' => [
                [
                    'label' => 'Sum of balances',
                    'data' => [...$data],
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getMaxHeight(): ?string
    {
        return '500px';
    }

    protected function getFilters(): ?array
    {
        $chartService = app(ChartService::class);

        return [
            'current_year' => now()->format('Y'),
            ...$chartService->getYearsForUserExceptCurrent() ?? [],
        ];
    }
}
