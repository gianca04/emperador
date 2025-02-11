<?php

namespace App\Filament\Resources\HabitacionTipoResource\Pages;

use App\Filament\Resources\HabitacionTipoResource;
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
}
