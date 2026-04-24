<?php

namespace App\Filament\Widgets;

use App\Services\ChartService;
use Carbon\Carbon;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Facades\Cache;

class Yearly extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Yearly Movements';

    protected bool $isCollapsible = true;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $activeYear = $this->filter['year'] ?? 'current_year';
        $activeBroker = $this->filter['broker'] ?? 0;

        $chartService = app(ChartService::class);

        $data = Cache::remember('yearly_chart_data_'.auth()->user()->id.'_'.$activeYear.'_'.$activeBroker, Carbon::now()->endOfDay()->subMinute(1), function () use ($chartService, $activeYear, $activeBroker) {
            return $chartService->getYearlyChartData($activeYear, $activeBroker);
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
            Select::make('year')
                ->options(fn () => [
                    'current_year' => now()->format('Y'),
                    ...$chartService->getYearsForUserExceptCurrent() ?? [],
                ])
                ->default('current_year')
                ->selectablePlaceholder(false),
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
