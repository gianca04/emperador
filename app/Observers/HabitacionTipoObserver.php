<?php

namespace App\Observers;

use App\Models\HabitacionTipo;

class HabitacionTipoObserver
{
    /**
     * Handle the HabitacionTipo "creating" event.
     * Se ejecuta antes de crear un nuevo registro
     */
    public function creating(HabitacionTipo $habitacionTipo): void
    {
        // Al crear, inicializar precios en 0 si no están definidos
        $habitacionTipo->precio_caracteristicas = $habitacionTipo->precio_caracteristicas ?? 0.00;
        $habitacionTipo->precio_final = $habitacionTipo->precio_base ?? 0.00;
    }

    /**
     * Handle the HabitacionTipo "created" event.
     * Se ejecuta después de crear un nuevo registro
     */
    public function created(HabitacionTipo $habitacionTipo): void
    {
        // Después de crear, si ya tiene características asignadas, calcular precio
        if ($habitacionTipo->caracteristicas()->count() > 0) {
            $this->recalcularPrecios($habitacionTipo);
        }
    }

    /**
     * Handle the HabitacionTipo "updating" event.
     * Se ejecuta antes de actualizar un registro
     */
    public function updating(HabitacionTipo $habitacionTipo): void
    {
        // Si cambió el precio base, recalcular automáticamente
        if ($habitacionTipo->isDirty('precio_base')) {
            $this->recalcularPrecios($habitacionTipo);
        }
    }

    /**
     * Handle the HabitacionTipo "updated" event.
     * Se ejecuta después de actualizar un registro
     */
    public function updated(HabitacionTipo $habitacionTipo): void
    {
        // Verificar si las características han cambiado y recalcular si es necesario
        if ($habitacionTipo->wasChanged(['precio_base'])) {
            $this->recalcularPrecios($habitacionTipo);
        }
    }

    /**
     * Método privado para recalcular precios
     */
    private function recalcularPrecios(HabitacionTipo $habitacionTipo): void
    {
        // Cargar características si no están cargadas
        if (!$habitacionTipo->relationLoaded('caracteristicas')) {
            $habitacionTipo->load('caracteristicas');
        }

        // Calcular precio de características
        $precioCaracteristicas = $habitacionTipo->caracteristicas->sum('precio');

        // Actualizar campos sin disparar eventos adicionales
        $habitacionTipo->precio_caracteristicas = round($precioCaracteristicas, 2);
        $habitacionTipo->precio_final = round((float) $habitacionTipo->precio_base + $precioCaracteristicas, 2);

        // Solo guardar si realmente hubo cambios para evitar bucles infinitos
        if ($habitacionTipo->isDirty(['precio_caracteristicas', 'precio_final'])) {
            $habitacionTipo->saveQuietly(); // saveQuietly no dispara eventos del modelo
        }
    }

    /**
     * Método público para recalcular precios manualmente
     */
    public static function recalcularPreciosManual(HabitacionTipo $habitacionTipo): void
    {
        $observer = new self();
        $observer->recalcularPrecios($habitacionTipo);
    }
}
