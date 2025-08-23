<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\HabitacionTipo;
use App\Observers\HabitacionTipoObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Registrar el Observer para HabitacionTipo
        HabitacionTipo::observe(HabitacionTipoObserver::class);
    }
}
