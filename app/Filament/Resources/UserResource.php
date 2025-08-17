<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
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
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\ImageColumn;



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
                Section::make('Información de Usuario') // Sección principal para capturar información básica del usuario
                    ->columns(3) // Organiza los campos en tres columnas
                    ->description('Ingrese los datos básicos del usuario. Todos los campos son obligatorios.') // Descripción de la sección
                    ->schema([
                        // Campo: Nombre de Usuario
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de Usuario') // Etiqueta del campo
                            ->placeholder('Ejemplo: Juan Pérez') // Texto de ayuda dentro del campo
                            ->required() // Campo obligatorio
                            ->maxLength(255), // Longitud máxima permitida

                        // Campo: Correo Electrónico
                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email() // Valida que el texto ingresado sea un email
                            ->placeholder('Ejemplo: usuario@dominio.com')
                            ->required()
                            ->maxLength(255),

                        // Campo: Contraseña (visible solo al crear, no al editar)
                        Forms\Components\TextInput::make('password')
                            ->hiddenOn('edit') // Oculta el campo al editar un registro
                            ->label('Contraseña')
                            ->password() // Muestra los caracteres como asteriscos
                            ->required()
                            ->maxLength(255),
                    ]),

                // Sección: Estado de usuario
                Section::make('Estado de usuario') // Controla el estado activo/inactivo del usuario
                    ->description('Activa o desactiva el estado del usuario.')
                    ->columns(2) // Organiza los elementos en dos columnas
                    ->schema([
                        // Campo: Estado actual del usuario (solo lectura)
                        Forms\Components\Placeholder::make('Estado')
                            ->label('Estado actual') // Etiqueta visible
                            ->content(fn(callable $get) => $get('active') ? '🟢 Activo' : '🔴 Inactivo') // Muestra el estado actual con un ícono
                            ->columnSpan(1), // Ocupa una columna

                        // Campo: Toggle para cambiar el estado
                        Forms\Components\Toggle::make('active')
                            ->label('Cambiar estado') // Etiqueta del toggle
                            ->onColor('success') // Color cuando está activado
                            ->offColor('danger') // Color cuando está desactivado
                            ->default(true) // Estado predeterminado: activo
                            ->required()
                            ->helperText('Cambia el estado del usuario entre activo e inactivo.') // Texto de ayuda
                            ->columnSpan(1), // Ocupa una columna
                    ]),

                // Sección: Fotografía
                Section::make('Fotografía') // Controla el estado activo/inactivo del usuario
                    ->description('Tamaño máximo en KB (2MB).')
                    ->schema([

                        FileUpload::make('photo')
                            ->label('Fotografía')
                            ->image()
                            ->directory('uploads/users')
                            ->required(),
                    ]),


                // Sección: Datos Personales
                Section::make('Datos Personales') // Captura información personal del usuario
                    ->columns(3) // Organiza los campos en tres columnas
                    ->description('Complete los datos personales. Algunos campos son opcionales.')
                    ->schema([
                        // Campo: DNI
                        Forms\Components\TextInput::make('dni')
                            ->label('DNI') // Documento Nacional de Identidad
                            ->placeholder('8 dígitos') // Ayuda al usuario indicando el formato esperado
                            ->required()
                            ->maxLength(8) // Limita el número de caracteres a 8
                            ->unique(ignoreRecord: true), // Asegura que el valor sea único, ignorando el registro actual

                        // Campo: Nombre
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->placeholder('Nombre(s)')
                            ->maxLength(255)
                            ->nullable(), // Permite valores nulos

                        // Campo: Apellido
                        Forms\Components\TextInput::make('apellido')
                            ->label('Apellido')
                            ->placeholder('Apellido(s)')
                            ->maxLength(255)
                            ->nullable(),

                        // Campo: Fecha de Nacimiento
                        Forms\Components\DatePicker::make('nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->placeholder('Seleccione una fecha')
                            ->nullable(),

                        // Campo: Teléfono
                        Forms\Components\TextInput::make('telefono')
                            ->label('Teléfono')
                            ->placeholder('9 dígitos')
                            ->maxLength(9)
                            ->tel() // Valida que sea un número telefónico
                            ->nullable(),

                        // Campo: Dirección
                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->placeholder('Dirección completa')
                            ->maxLength(255)
                            ->nullable(),
                    ]),

                // Sección: Ubicación
                Section::make('Ubicación') // Permite seleccionar la ubicación del usuario
                    ->columns(3) // Organiza los campos en tres columnas
                    ->schema([
                        // Campo: Departamento
                        Select::make('departamento_id')
                            ->label('Departamento') // Etiqueta del campo
                            ->options(Departamento::all()->pluck('name', 'id')) // Opciones de departamentos
                            ->reactive() // Actualiza dinámicamente los siguientes campos
                            ->afterStateUpdated(function (callable $set) {
                                // Limpia provincia y distrito cuando se cambia el departamento
                                $set('provincia_id', null);
                                $set('distrito_id', null);
                            }),

                        // Campo: Provincia
                        Select::make('provincia_id')
                            ->label('Provincia') // Etiqueta del campo
                            ->options(function (callable $get) {
                                $departamentoId = $get('departamento_id'); // Obtiene el ID del departamento seleccionado
                                return $departamentoId
                                    ? Provincia::where('departamento_id', $departamentoId)->pluck('name', 'id')
                                    : [];
                            })
                            ->reactive() // Actualiza dinámicamente los distritos
                            ->afterStateUpdated(function (callable $set) {
                                // Limpia el distrito cuando se cambia la provincia
                                $set('distrito_id', null);
                            })
                            ->disabled(fn(callable $get) => !$get('departamento_id')), // Desactiva si no hay un departamento seleccionado

                        // Campo: Distrito
                        Select::make('distrito_id')
                            ->label('Distrito') // Etiqueta del campo
                            ->options(function (callable $get) {
                                $provinciaId = $get('provincia_id'); // Obtiene el ID de la provincia seleccionada
                                return $provinciaId
                                    ? Distrito::where('provincia_id', $provinciaId)->pluck('name', 'id')
                                    : [];
                            })
                            ->disabled(fn(callable $get) => !$get('provincia_id')), // Desactiva si no hay una provincia seleccionada
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('photo')
                    ->label('Foto')
                    ->circular()
                    ->height(50),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Correo'),

                // Columna para mostrar el estado (activo o inactivo) con un badge visual
                Tables\Columns\BadgeColumn::make('active')
                    ->label('Estado') // Etiqueta para la columna
                    ->formatStateUsing(fn(bool $state): string => $state ? '🟢 Activo' : '🔴 Inactivo') // Formato visual del estado
                    ->color(fn(bool $state): string => $state ? 'success' : 'danger'), // Color del badge dependiendo del estado

                // Columna para el nombre con búsqueda habilitada
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                // Columna para el email con búsqueda habilitada
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),

                // Columna para el DNI con búsqueda habilitada
                Tables\Columns\TextColumn::make('dni')
                    ->searchable(),

                // Columna para el nombre con búsqueda y ordenamiento habilitados
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable()
                    ->sortable(),

                // Columna para el apellido con búsqueda y ordenamiento habilitados
                Tables\Columns\TextColumn::make('apellido')
                    ->searchable()
                    ->sortable(),

                // Columna para el teléfono con búsqueda habilitada
                Tables\Columns\TextColumn::make('telefono')
                    ->searchable(),

                // Columna para la dirección (sin búsqueda ni ordenamiento)
                Tables\Columns\TextColumn::make('direccion'),

                // Columna para el distrito con etiqueta personalizada, ordenamiento, búsqueda y visibilidad alternable
                Tables\Columns\TextColumn::make('distrito.name')
                    ->label('Distrito') // Etiqueta personalizada
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true), // Oculto por defecto

                // Columna para la provincia con configuración similar a distrito
                Tables\Columns\TextColumn::make('provincia.name')
                    ->label('Provincia') // Etiqueta personalizada
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Columna para el departamento con configuración similar a distrito
                Tables\Columns\TextColumn::make('departamento.name')
                    ->label('Departamento') // Etiqueta personalizada
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Columna para la fecha de creación con formato de fecha y hora, ordenamiento y visibilidad alternable
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Columna para la fecha de verificación del email con configuración similar a created_at
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                // Columna para la fecha de última actualización con configuración similar a created_at
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtro para mostrar solo registros activos
                Filter::make('Activo')
                    ->query(fn(Builder $query) => $query->where('active', true))
                    ->toggle(),

                // Filtro para mostrar solo registros inactivos
                Filter::make('Inactivo')
                    ->query(fn(Builder $query) => $query->where('active', false))
                    ->toggle(),
            ])
            ->actions([
                // Acción para editar registros
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Acción grupal para eliminar registros seleccionados
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
