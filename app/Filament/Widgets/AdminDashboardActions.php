<?php

namespace App\Filament\Widgets;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Support\Colors\Color;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Cache;

class AdminDashboardActions extends Widget implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    protected string $view = 'filament.widgets.admin-dashboard-actions';

    protected int|string|array $columnSpan = 'full';

    public function clearCacheAction(): Action
    {
        return Action::make('clearCache')
            ->label('Clear All Caches')
            ->icon('heroicon-o-trash')
            ->color(Color::Green)
            ->action(function (): void {
                try {
                    Cache::flush();

                    Notification::make()
                        ->title('Success')
                        ->body('Cache cleared successfully.')
                        ->success()
                        ->send();
                } catch (Exception $e) {
                    Notification::make()
                        ->title('Error')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }
}
