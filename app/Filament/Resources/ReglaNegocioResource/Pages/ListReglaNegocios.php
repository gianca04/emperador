<?php

namespace App\Filament\Resources\ReglaNegocioResource\Pages;

use App\Filament\Resources\ReglaNegocioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListReglaNegocios extends ListRecords
{
    protected static string $resource = ReglaNegocioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
