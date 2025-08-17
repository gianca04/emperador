<?php

namespace App\Forms\Components;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms;

class caracteristicaForm
{

    public static function make(): array
    {
        return [
            Forms\Components\Section::make('Información General')
                    ->columns(2)
                    ->description('Datos principales de la característica.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la característica')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Aire Acondicionado')
                            ->unique(table: 'caracteristicas', column: 'name', ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                                'max' => 'El nombre no puede superar los 255 caracteres.',
                                'unique' => 'Esta característica ya existe.',
                            ]),

                        Forms\Components\TextInput::make('precio')
                            ->label('Precio adicional')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('S/')
                            ->validationMessages([
                                'required' => 'El precio es obligatorio.',
                                'numeric' => 'Debe ingresar un valor numérico.',
                                'min' => 'El precio no puede ser negativo.',
                            ]),
                    ]),

                Forms\Components\Section::make('Configuraciones')
                    ->columns(2)
                    ->description('Define si la característica está activa y si se puede quitar.')
                    ->schema([
                        Forms\Components\Toggle::make('activa')
                            ->label('¿Está activa?')
                            ->required()
                            ->default(true),

                        Forms\Components\Toggle::make('removible')
                            ->label('¿Es removible?')
                            ->required(),
                    ]),
        ];
    }
}
