<?php

namespace App\Filament\Widgets;

use App\Services\ChartService;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Facades\Cache;

class Weekly extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Weekly Movements';

    protected bool $isCollapsible = true;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {

        $chartService = app(ChartService::class);
        $activeBroker = $this->filter['broker'] ?? 0;

        $data = Cache::remember('weekly_chart_data'.auth()->user()->id.'_'.$activeBroker, Carbon::now()->endOfDay()->subMinute(1), function () use ($chartService, $activeBroker) {
            return $chartService->getWeeklyChartData($activeBroker);
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

    public function filtersSchema(Schema $schema): Schema
    {
        $chartService = app(ChartService::class);

        return $schema->components([
            Select::make('broker')
                ->options(fn () => [
                    0 => 'All',
                    ...$chartService->getAllBrokerForAccountIdLabel(),
                ])
                ->searchable()
                ->preload()
                ->default(0)
                ->selectablePlaceholder(false),
        ]);
    }
}
