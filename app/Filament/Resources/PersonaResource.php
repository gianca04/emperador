<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonaResource\Pages;
use App\Models\Persona;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class PersonaResource extends Resource
{
    protected static ?string $model = Persona::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(100)
                    ->rules(['regex:/^[\pL\s]+$/u'])
                    ->validationMessages([
                        'required' => 'El nombre es obligatorio.',
                        'max' => 'El nombre no debe exceder los 100 caracteres.',
                        'regex' => 'El nombre solo puede contener letras y espacios.',
                    ]),

                TextInput::make('apellido')
                    ->label('Apellido')
                    ->required()
                    ->maxLength(100)
                    ->rules(['regex:/^[\pL\s]+$/u'])
                    ->validationMessages([
                        'required' => 'El apellido es obligatorio.',
                        'max' => 'El apellido no debe exceder los 100 caracteres.',
                        'regex' => 'El apellido solo puede contener letras y espacios.',
                    ]),

                Select::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->options([
                        'DNI' => 'DNI',
                        'CARNET EXT' => 'Carnet de Extranjería',
                        'PASAPORTE' => 'Pasaporte',
                        'OTROS' => 'Otros',
                    ])
                    ->required()
                    ->validationMessages([
                        'required' => 'Debe seleccionar un tipo de documento.',
                    ])
                    ->reactive(),

                TextInput::make('numero_documento')
                    ->label('Número de Documento')
                    ->required()
                    ->maxLength(15)
                    ->rule(function (callable $get) {
                        $tipo = $get('tipo_documento');
                        return match ($tipo) {
                            'DNI' => ['regex:/^\d{8}$/', 'size:8'],
                            'CARNET EXT', 'PASAPORTE' => ['max:12'],
                            'OTROS' => ['size:15'],
                            default => [],
                        };
                    })
                    ->validationMessages([
                        'required' => 'El número de documento es obligatorio.',
                        'regex' => 'El número de documento no tiene el formato correcto.',
                        'size' => 'La longitud del documento debe ser exacta.',
                        'max' => 'El número de documento no debe superar los 12 caracteres.',
                    ]),

                TextInput::make('email')
                    ->label('Correo Electrónico')
                    ->email()
                    ->unique(Persona::class, 'email')
                    ->nullable()
                    ->validationMessages([
                        'email' => 'El correo electrónico no es válido.',
                        'unique' => 'El correo electrónico ya está registrado.',
                    ]),

                TextInput::make('telefono')
                    ->label('Teléfono')
                    ->nullable()
                    ->maxLength(9)
                    ->regex('/^\d{9}$/')
                    ->validationMessages([
                        'regex' => 'El teléfono debe tener 9 dígitos numéricos.',
                    ]),

                TextInput::make('telefono_secundario')
                    ->label('Teléfono Secundario')
                    ->nullable()
                    ->maxLength(9)
                    ->regex('/^\d{9}$/')
                    ->validationMessages([
                        'regex' => 'El teléfono secundario debe tener 9 dígitos numéricos.',
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')->label('Nombre'),
                TextColumn::make('apellido')->label('Apellido'),
                TextColumn::make('tipo_documento')->label('Tipo de Documento'),
                TextColumn::make('numero_documento')->label('Número de Documento'),
                TextColumn::make('email')->label('Correo Electrónico')->sortable(),
                TextColumn::make('telefono')->label('Teléfono'),
            ])
            ->filters([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPersonas::route('/'),
            'create' => Pages\CreatePersona::route('/create'),
            'edit' => Pages\EditPersona::route('/{record}/edit'),
        ];
    }
}
