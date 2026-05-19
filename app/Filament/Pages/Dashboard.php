<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminOverView;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\Cache;

class Dashboard extends BaseDashboard
{
    protected function getActions(): array
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

    protected function getHeaderWidgets(): array
    {
        return [
            AdminOverView::class,
        ];
    }
}
