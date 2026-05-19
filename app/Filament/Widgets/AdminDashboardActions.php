<?php

namespace App\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class AdminDashboardActions extends Widget implements HasActions
{
    use InteractsWithActions;

    protected string $view = 'filament.widgets.admin-dashboard-actions';

    protected int|string|array $columnSpan = 'full';

    public function actions(): array
    {
        return [
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
        ];
    }
}
