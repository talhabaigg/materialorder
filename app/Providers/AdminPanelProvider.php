<?php

namespace App\Providers;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use App\Filament\Auth\Login;
use Awcodes\Curator\CuratorPlugin;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Http\Middleware\Authenticate;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Awcodes\FilamentGravatar\GravatarPlugin;
use Pboivin\FilamentPeek\FilamentPeekPlugin;
use Awcodes\FilamentGravatar\GravatarProvider;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use BezhanSalleh\FilamentExceptions\FilamentExceptionsPlugin;
use Croustibat\FilamentJobsMonitor\FilamentJobsMonitorPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->sidebarCollapsibleOnDesktop()
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            // ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->spa()
            ->databaseNotifications()
            ->plugins([
                \TomatoPHP\FilamentPWA\FilamentPWAPlugin::make(),
                \TomatoPHP\FilamentSettingsHub\FilamentSettingsHubPlugin::make()
                    ->allowShield(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        hasAvatars: false
                    )
                    ->enableTwoFactorAuthentication(),
                FilamentJobsMonitorPlugin::make()
                    ->enableNavigation(
                        fn() => auth()->user()->hasRole('super_admin') || auth()->user()->can('view_any_queue_job)'),
                    )
                    ->navigationCountBadge()
                    ->navigationGroup('Settings'),
                FilamentPeekPlugin::make()
                    ->disablePluginStyles(),
                FilamentExceptionsPlugin::make(),
                // GravatarPlugin::make(),
            ])
            // ->defaultAvatarProvider(GravatarProvider::class)
            // ->favicon(asset('/favicon-32x32.png'))
            ->favicon(asset('/superior-group-logo.svg'))
            ->brandLogo(fn() => view('components.logo'))
            ->navigationGroups([
                'Main',
                'Admin',
                'Settings',
            ])
            ->maxContentWidth(MaxWidth::Full)
            ->colors([
                'primary' => Color::Blue,
            ])
            ->viteTheme('resources/css/admin.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
