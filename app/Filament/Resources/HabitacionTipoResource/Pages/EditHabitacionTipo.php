<?php

namespace App\Filament\Resources\HabitacionTipoResource\Pages;

use App\Filament\Resources\HabitacionTipoResource;
use App\Observers\HabitacionTipoObserver;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHabitacionTipo extends EditRecord
{
    protected static string $resource = HabitacionTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Después de guardar, sincronizar características y recalcular precios
        $caracteristicasIds = $this->data['caracteristicas'] ?? [];

        $this->record->caracteristicas()->sync($caracteristicasIds);
        // Usar el observer para recalcular precios
        HabitacionTipoObserver::recalcularPreciosManual($this->record);
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Al cargar el formulario, asegurar que los precios calculados estén actualizados
        $habitacionTipo = $this->record;

        if ($habitacionTipo) {
            $habitacionTipo->load('caracteristicas');
            $precioCaracteristicas = $habitacionTipo->caracteristicas->sum('precio');

            $data['precio_caracteristicas'] = number_format($precioCaracteristicas, 2, '.', '');
            $data['precio_final'] = number_format((float) $habitacionTipo->precio_base + $precioCaracteristicas, 2, '.', '');
        }

        return $data;
    }
}
