<?php

namespace App\Filament\Resources\HabitacionTipoResource\Pages;

use App\Filament\Resources\HabitacionTipoResource;
use App\Observers\HabitacionTipoObserver;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHabitacionTipo extends CreateRecord
{
    protected static string $resource = HabitacionTipoResource::class;

    protected function afterCreate(): void
    {
        // Después de crear el modelo, sincronizar características y recalcular precios
        $caracteristicasIds = $this->data['caracteristicas'] ?? [];

        if (!empty($caracteristicasIds)) {
            $this->record->caracteristicas()->sync($caracteristicasIds);
            // Usar el observer para recalcular precios
            HabitacionTipoObserver::recalcularPreciosManual($this->record);
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asegurar que los precios calculados se incluyan en los datos
        $precioBase = (float) ($data['precio_base'] ?? 0);
        $precioCaracteristicas = 0;

        if (!empty($data['caracteristicas'])) {
            $caracteristicas = \App\Models\Caracteristica::whereIn('id', $data['caracteristicas'])->get();
            $precioCaracteristicas = $caracteristicas->sum('precio');
        }

        $data['precio_caracteristicas'] = round($precioCaracteristicas, 2);
        $data['precio_final'] = round($precioBase + $precioCaracteristicas, 2);

        return $data;
    }
}
