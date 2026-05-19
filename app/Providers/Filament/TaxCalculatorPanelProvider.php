<?php

namespace App\Providers\Filament;

use App\Filament\Pages\EditProfile;
use App\Filament\Pages\Register;
use App\Filament\TaxCalculator\Pages\Dashboard;
use Carbon\Carbon;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class TaxCalculatorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('taxCalculator')
            ->path('taxCalculator')
            ->login()
            ->registration(Register::class)
            ->profile(EditProfile::class, isSimple: false)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandLogo(fn () => view('filament.brand-logo'))
            ->darkModeBrandLogo(fn () => view('filament.brand-logo'))
            ->favicon(asset('favicon.svg'))
            ->discoverResources(in: app_path('Filament/TaxCalculator/Resources'), for: 'App\Filament\TaxCalculator\Resources')
            ->discoverPages(in: app_path('Filament/TaxCalculator/Pages'), for: 'App\Filament\TaxCalculator\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/TaxCalculator/Widgets'), for: 'App\Filament\TaxCalculator\Widgets')
            ->widgets([
            ])
            ->navigationGroups([
                NavigationGroup::make()
                    ->label('Forex')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Daily Changes')
                    ->collapsed(false),
                NavigationGroup::make()
                    ->label('Broker Statuses')
                    ->collapsed(false),
            ])
            ->renderHook(PanelsRenderHook::FOOTER, fn () => view('footer', ['Year' => Carbon::now()->format('Y')]))
            ->sidebarFullyCollapsibleOnDesktop()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->spa()
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
