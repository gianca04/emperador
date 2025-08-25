<?php

namespace App\Filament\Resources\ReglaNegocioResource\Pages;

use App\Filament\Resources\ReglaNegocioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReglaNegocio extends EditRecord
{
    protected static string $resource = ReglaNegocioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
