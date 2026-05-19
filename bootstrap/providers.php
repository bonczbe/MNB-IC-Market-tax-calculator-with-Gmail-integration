<?php

use App\Providers\AppServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\TaxCalculatorPanelProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\HorizonServiceProvider;

return [
    AppServiceProvider::class,
    AdminPanelProvider::class,
    TaxCalculatorPanelProvider::class,
    FortifyServiceProvider::class,
    HorizonServiceProvider::class,
];
