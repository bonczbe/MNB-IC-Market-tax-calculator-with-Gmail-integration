<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminDashboardActions;
use App\Filament\Widgets\AdminOverView;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getActions(): array
    {
        return [
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            AdminDashboardActions::class,
            AdminOverView::class,
        ];
    }
}
