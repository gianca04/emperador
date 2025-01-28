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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                            ->reactive()
                            ->afterStateUpdated(fn(callable $set) => $set('provincia_id', null))
                            ->afterStateUpdated(fn(callable $set) => $set('distrito_id', null))
                            ->afterStateHydrated(function (callable $set, $state) {
                                if ($state) {
                                    $provincia = Provincia::where('departamento_id', $state)->first();
                                    if ($provincia) {
                                        $set('provincia_id', $provincia->id);
                                    }
                                }
                            })
                            ->required(),

                        Select::make('provincia_id')
                            ->label('Provincia')
                            ->options(function (callable $get) {
                                $departamentoId = $get('departamento_id');
                                if ($departamentoId) {
                                    return Provincia::where('departamento_id', $departamentoId)->pluck('name', 'id');
                                }
                                return [];
                            })
                            ->reactive()
                            ->afterStateUpdated(fn(callable $set) => $set('distrito_id', null))
                            ->afterStateHydrated(function (callable $set, $state) {
                                if ($state) {
                                    $distrito = Distrito::where('provincia_id', $state)->first();
                                    if ($distrito) {
                                        $set('distrito_id', $distrito->id);
                                    }
                                }
                            })
                            ->required(),

                        Select::make('distrito_id')
                            ->label('Distrito')
                            ->relationship('distrito', 'name') // Usa la relación para obtener automáticamente los nombres
                            ->options(function (callable $get) {
                                $provinciaId = $get('provincia_id');
                                if ($provinciaId) {
                                    // Retorna los distritos filtrados por provincia
                                    return Distrito::where('provincia_id', $provinciaId)->pluck('name', 'id');
                                }
                                return [];
                            })
                            ->searchable() // Permite buscar en las opciones
                            ->preload()    // Carga las opciones al inicio
                            ->required(),  // Campo obligatorio
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
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
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
