<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Illuminate\Validation\Rule;
use Filament\Forms\Components\Select; // Para usar el componente Select
use Illuminate\Support\Facades\DB; // Para consultas adicionales si las necesitas
use App\Models\Departamento;
use App\Models\Provincia;
use App\Models\Distrito;
use Filament\Notifications\Collection;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Set;
use Filament\Forms\Get;
use Illuminate\Database\Eloquent\Model;


class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Empleados';

    protected static ?string $navigationGroup = 'Administrar Sistema';

    protected static ?int $navigationSort = 2;
    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Sección: Información de usuario
                Section::make('Información de Usuario')
                    ->columns(3)
                    ->description('Ingrese los datos básicos del usuario. Todos los campos son obligatorios.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de Usuario')
                            ->placeholder('Ejemplo: Juan Pérez')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->placeholder('Ejemplo: usuario@dominio.com')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('password')
                            ->hiddenOn('edit')
                            ->label('Contraseña')
                            ->password()
                            ->required()
                            ->maxLength(255),
                    ]),

                // Sección: Datos Personales
                Section::make('Datos Personales')
                    ->columns(3)
                    ->description('Complete los datos personales. Algunos campos son opcionales.')
                    ->schema([
                        Forms\Components\TextInput::make('dni')
                            ->label('DNI')
                            ->placeholder('8 dígitos')
                            ->required()
                            ->maxLength(8)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->placeholder('Nombre(s)')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\TextInput::make('apellido')
                            ->label('Apellido')
                            ->placeholder('Apellido(s)')
                            ->maxLength(255)
                            ->nullable(),

                        Forms\Components\DatePicker::make('nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->placeholder('Seleccione una fecha')
                            ->nullable(),

                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->placeholder('9 dígitos')
                            ->maxLength(9)
                            ->tel()
                            ->nullable(),

                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->placeholder('Dirección completa')
                            ->maxLength(255)
                            ->nullable(),
                    ]),

                // Sección: Ubicación
                Section::make('Ubicación')
                    ->columns(3)
                    ->schema([
                        Select::make('departamento_id')
                            ->label('Departamento')
                            ->options(Departamento::all()->pluck('name', 'id'))
                            ->reactive() // Activa la reactividad para actualizar los siguientes campos
                            ->afterStateUpdated(function (callable $set) {
                                // Al cambiar el departamento, limpia los campos dependientes
                                $set('provincia_id', null);
                                $set('distrito_id', null);
                            }),

                        Select::make('provincia_id')
                            ->label('Provincia')
                            ->options(function (callable $get) {
                                $departamentoId = $get('departamento_id'); // Obtén el departamento seleccionado
                                return $departamentoId
                                    ? Provincia::where('departamento_id', $departamentoId)->pluck('name', 'id')
                                    : [];
                            })
                            ->reactive() // Activa la reactividad para actualizar distritos
                            ->afterStateUpdated(function (callable $set) {
                                // Al cambiar la provincia, limpia el distrito
                                $set('distrito_id', null);
                            })
                            ->disabled(fn(callable $get) => !$get('departamento_id')), // Desactiva si no hay departamento seleccionado

                        Select::make('distrito_id')
                            ->label('Distrito')
                            ->options(function (callable $get) {
                                $provinciaId = $get('provincia_id'); // Obtén la provincia seleccionada
                                return $provinciaId
                                    ? Distrito::where('provincia_id', $provinciaId)->pluck('name', 'id')
                                    : [];
                            })
                            ->disabled(fn(callable $get) => !$get('provincia_id')), // Desactiva si no hay provincia seleccionada
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('dni')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('apellido')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('direccion')
                    ->sortable(),

                Tables\Columns\TextColumn::make('distrito.name')
                    ->label('Distrito') // Cambia el label a tu gusto
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('provincia.name')
                    ->label('Provincia') // Cambia el label a tu gusto
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('departamento.name')
                    ->label('Departamento') // Cambia el label a tu gusto
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),


                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
