<?php

namespace App\Filament\Resources\OrganizacionResource\Pages;

use App\Filament\Resources\OrganizacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizacions extends ListRecords
{
    protected static string $resource = OrganizacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
