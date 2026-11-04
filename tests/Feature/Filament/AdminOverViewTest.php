<?php

use App\Filament\Widgets\AdminOverView;
use Livewire\Livewire;

it('renders dashboard sections as schema components', function () {
    Livewire::test(AdminOverView::class)
        ->assertSee('Config Settings')
        ->assertSee('Last Fetches and Calculations')
        ->assertSee('Other Stats')
        ->assertSee('Registered Users');
});
