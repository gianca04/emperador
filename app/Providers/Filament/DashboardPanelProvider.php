<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\Support\Assets\Js;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class DashboardPanelProvider extends PanelProvider
{


    public function panel(Panel $panel): Panel
    {
        return $panel
            ->assets([
                Js::make('flowbite', 'https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.0/flowbite.min.js'),
            ])
            ->sidebarCollapsibleOnDesktop()
            ->default()
            ->id('dashboard')
            ->path('dashboard')
            ->login()
            ->colors([
                'primary' => '#1E40AF', // Azul vibrante para llamadas a la acción
                'secondary' => '#4B5563', // Gris medio para elementos secundarios y textos
                'accent' => '#d946ef', // Fucsia para detalles destacados
                'success' => '#16a34a', // Verde para notificaciones o estados positivos
                'danger' => '#dc2626', // Rojo para alertas o errores
                'warning' => '#f59e0b', // Amarillo/naranja para advertencias
                'info' => '#0ea5e9', // Azul claro para información general
                'gray' => [
                    50 => '#f9fafb',
                    100 => '#f3f4f6',
                    200 => '#e5e7eb',
                    300 => '#d1d5db',
                    400 => '#9ca3af',
                    500 => '#6b7280',
                    600 => '#4b5563',
                    700 => '#374151',
                    800 => '#1f2937',
                    900 => '#111827',
                    950 => '#030712',
                ]
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                //Widgets\FilamentInfoWidget::class,
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
            ->plugin(\TomatoPHP\FilamentTranslationsGoogle\FilamentTranslationsGooglePlugin::make())
            ->authMiddleware([
                Authenticate::class,
            ]);
            
    }

}
