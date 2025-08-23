<?php

namespace App\Filament\Pages;

use App\Models\Habitacion;
use Filament\Pages\Page;

class Habitaciones extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.habitaciones'; // Vista personalizada

    protected static ?string $navigationLabel = 'Vista de Habitaciones';

    // Método para cargar las habitaciones
    public $habitaciones;

    public function mount()
    {
        // Cargar todas las habitaciones con sus relaciones y pasarlas a la vista
        $this->habitaciones = Habitacion::with(['tipo.caracteristicas', 'caracteristicas'])->get();
    }
}
