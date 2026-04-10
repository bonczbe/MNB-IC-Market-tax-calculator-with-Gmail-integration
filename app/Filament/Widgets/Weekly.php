<?php

namespace App\Filament\Widgets;

use App\Services\ChartService;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Cache;

class Weekly extends ChartWidget
{
    protected ?string $heading = 'Weekly Movements';


    protected bool $isCollapsible = true;

    protected int | string | array $columnSpan = 'full';

    protected function getData(): array
    {

        $chartService = app(ChartService::class);

        $data = Cache::remember('weekly_chart_data'.auth()->user()->id, Carbon::now()->endOfDay()->subMinute(1), function () use ($chartService) {
            return $chartService->getWeeklyChartData();
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
}
