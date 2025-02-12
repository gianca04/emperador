<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrganizacionResource\Pages;
use App\Models\Organizacion;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class OrganizacionResource extends Resource
{
    protected static ?string $model = Organizacion::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label('Nombre de la organización')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'El nombre de la organización es obligatorio.',
                        'max' => 'El nombre no debe exceder los 255 caracteres.',
                    ]),

                TextInput::make('ruc')
                    ->label('RUC')
                    ->required()
                    ->maxLength(11)
                    ->minLength(11)
                    ->numeric()
                    ->unique(Organizacion::class, 'ruc')
                    ->validationMessages([
                        'required' => 'El RUC es obligatorio.',
                        'min' => 'El RUC debe tener exactamente 11 dígitos.',
                        'max' => 'El RUC debe tener exactamente 11 dígitos.',
                        'numeric' => 'El RUC solo puede contener números.',
                        'unique' => 'Este RUC ya está registrado.',
                    ]),

                Select::make('tipo_ruc')
                    ->label('Tipo de RUC')
                    ->options([
                        '10' => 'RUC 10 - Persona Natural',
                        '20' => 'RUC 20 - Empresa',
                    ])
                    ->native(false)
                    ->required()
                    ->validationMessages([
                        'required' => 'Debe seleccionar un tipo de RUC.',
                    ]),

                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->required()
                    ->maxLength(20)
                    ->regex('/^\d{6,20}$/')
                    ->validationMessages([
                        'required' => 'El teléfono es obligatorio.',
                        'regex' => 'El teléfono debe contener entre 6 y 20 dígitos numéricos.',
                    ]),

                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->required()
                    ->unique(Organizacion::class, 'email')
                    ->maxLength(100)
                    ->validationMessages([
                        'required' => 'El correo electrónico es obligatorio.',
                        'email' => 'Debe ingresar un correo electrónico válido.',
                        'unique' => 'Este correo ya está registrado.',
                    ]),

                TextInput::make('direccion')
                    ->label('Dirección')
                    ->nullable()
                    ->maxLength(500)
                    ->validationMessages([
                        'max' => 'La dirección no debe exceder los 500 caracteres.',
                    ]),

                TextInput::make('nombre_contacto')
                    ->label('Nombre del Contacto')
                    ->required()
                    ->maxLength(255)
                    ->validationMessages([
                        'required' => 'El nombre del contacto es obligatorio.',
                        'max' => 'El nombre del contacto no debe exceder los 255 caracteres.',
                    ]),

                TextInput::make('telefono_contacto')
                    ->label('Teléfono del Contacto')
                    ->required()
                    ->maxLength(20)
                    ->regex('/^\d{6,20}$/')
                    ->validationMessages([
                        'required' => 'El teléfono del contacto es obligatorio.',
                        'regex' => 'El teléfono del contacto debe contener entre 6 y 20 dígitos numéricos.',
                    ]),

                TextInput::make('telefono_secundario')
                    ->label('Teléfono Secundario')
                    ->nullable()
                    ->maxLength(20)
                    ->regex('/^\d{6,20}$/')
                    ->validationMessages([
                        'regex' => 'El teléfono secundario debe contener entre 6 y 20 dígitos numéricos.',
                    ]),

                TextInput::make('email_contacto')
                    ->label('Correo del Contacto')
                    ->email()
                    ->maxLength(100)
                    ->validationMessages([
                        'email' => 'Debe ingresar un correo electrónico válido.',
                    ]),

                Select::make('tipo_organizacion')
                    ->label('Tipo de Organización')
                    ->options([
                        'EMPRESA' => 'Empresa',
                        'ONG' => 'ONG',
                        'GOBIERNO' => 'Gobierno',
                        'OTRA' => 'Otra',
                    ])
                    ->native(false)
                    ->required()
                    ->validationMessages([
                        'required' => 'Debe seleccionar un tipo de organización.',
                    ]),

                TextInput::make('fecha_registro')
                    ->label('Fecha de Registro')
                    ->default(now())
                    ->disabled(),

                TextInput::make('notas')
                    ->label('Notas')
                    ->maxLength(1000)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nombre'),
                TextColumn::make('ruc')->label('RUC'),
                TextColumn::make('tipo_ruc')->label('Tipo de RUC'),
                TextColumn::make('telefono')->label('Teléfono'),
                TextColumn::make('email')->label('Correo Electrónico')->sortable(),
                TextColumn::make('tipo_organizacion')->label('Tipo de Organización'),
                TextColumn::make('fecha_registro')->label('Fecha de Registro')->sortable(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrganizacions::route('/'),
            'create' => Pages\CreateOrganizacion::route('/create'),
            'edit' => Pages\EditOrganizacion::route('/{record}/edit'),
        ];
    }
}
