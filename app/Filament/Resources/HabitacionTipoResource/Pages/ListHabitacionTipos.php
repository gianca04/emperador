<?php

namespace App\Filament\Resources\HabitacionTipoResource\Pages;

use App\Filament\Resources\HabitacionTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHabitacionTipos extends ListRecords
{
    protected static string $resource = HabitacionTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
