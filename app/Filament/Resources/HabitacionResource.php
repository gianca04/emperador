<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HabitacionResource\Pages;
use App\Filament\Resources\HabitacionResource\RelationManagers;
use App\Models\Habitacion;
use App\Models\Caracteristica;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;

use App\Models\HabitacionTipo;

use Filament\Forms;
use Illuminate\Validation\Rule;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HabitacionResource extends Resource
{
    protected static ?string $model = Habitacion::class;

    protected static ?string $navigationIcon = 'icon-habitacion';
    protected static ?string $navigationLabel = 'Habitaciones';
    protected static ?string $navigationGroup = 'Gestión de Habitaciones';

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Información General')
                ->columns(2)
                ->description('Datos principales de la habitación, como ubicación, número y estado.')
                ->schema([
                    Forms\Components\Select::make('ubicacion')
                        ->label('Ubicación')
                        ->options([
                            '1' => 'Primer Piso',
                            '2' => 'Segundo Piso',
                            '3' => 'Tercer Piso',
                            '4' => 'Cuarto Piso',
                        ])
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(fn($state, callable $set) => $set('numero', $state . '00'))
                        ->validationMessages(['required' => 'La ubicación es obligatoria.']),

                    Forms\Components\TextInput::make('numero')
                        ->label('Número de Habitación')
                        ->required()
                        ->numeric()
                        ->rules(fn($record) => [
                            Rule::unique('habitaciones', 'numero')->ignore($record?->id),
                        ])
                        ->reactive()
                        ->placeholder('Ej: 101, 202, 303')
                        ->validationMessages([
                            'required' => 'El número de habitación es obligatorio.',
                            'numeric' => 'Debe ser un número válido.',
                            'unique' => 'Este número de habitación ya existe.',
                        ]),

                    Forms\Components\Select::make('estado')
                        ->label('Estado Actual')
                        ->options([
                            'Disponible' => 'Disponible',
                            'Limpiar' => 'Limpiar',
                            'Deshabilitada' => 'Deshabilitada',
                            'Mantenimiento' => 'Mantenimiento',
                            'Ocupada' => 'Ocupada',
                        ])
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default('Disponible')
                        ->validationMessages(['required' => 'El estado de la habitación es obligatorio.']),

                    Forms\Components\Select::make('habitacion_tipo_id')
                        ->label('Tipo de Habitación')
                        ->relationship('tipo', 'name')
                        ->preload()
                        ->searchable()
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (!$state) {

                                $set('caracteristicas', []);
                                $set('precio_base', null);
                                $set('precio_caracteristicas', null);
                                $set('precio_final', null);
                                return;
                            }

                            $tipoHabitacion = HabitacionTipo::find($state);
                            if ($tipoHabitacion) {
                                // Recuperamos directamente los precios desde la base de datos
                                $set('precio_base', $tipoHabitacion->precio_base);
                                $set('precio_caracteristicas', $tipoHabitacion->precio_caracteristicas);
                                $set('precio_final', $tipoHabitacion->precio_final);

                                // Recuperamos las características asociadas
                                $caracteristicas = $tipoHabitacion->caracteristicas()->pluck('caracteristicas.id')->toArray();
                                $set('caracteristicas', $caracteristicas);
                            } else {
                                $set('caracteristicas', []);
                                $set('precio_base', null);
                                $set('precio_caracteristicas', null);
                                $set('precio_final', null);
                            }

                            $set('precio_final', (float) $get('precio_base') + (float) $get('precio_caracteristicas'));
                        })
                        ->validationMessages(['required' => 'Selecciona un tipo de habitación.']),
                ]),

            Forms\Components\Section::make('Detalles de la Habitación')
                ->columns(2)
                ->description('Características y especificaciones adicionales.')
                ->schema([

                    Forms\Components\Select::make('caracteristicas')
                        ->label('Características')
                        ->relationship('caracteristicas', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->options(
                            fn() =>
                            Caracteristica::query()
                                ->select('caracteristicas.id', 'caracteristicas.name', 'caracteristicas.precio')
                                ->orderBy('caracteristicas.name')
                                ->get()
                                ->mapWithKeys(fn($caracteristica) => [
                                    $caracteristica->id => "{$caracteristica->name} - " .
                                        ($caracteristica->precio == 0.00 ? "Incluida" : "S/ {$caracteristica->precio}"),
                                ])
                        )
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            // Calcular la suma de los precios de las caracteristicas selecionadas
                            $caracteristicasPrecios = Caracteristica::whereIn('id', $state)->pluck('precio');

                            // Actualizar el campo de precio de características seleccionds
                            $precioCaracteristicas = $caracteristicasPrecios->sum();

                            // Recalcular el precio final
                            $set('precio_caracteristicas', $precioCaracteristicas);
                            $set('precio_final', (float) $get('precio_base') + $precioCaracteristicas);
                        })
                        ->validationMessages(['array' => 'Las características deben ser una lista válida.']),

                    Forms\Components\Textarea::make('descripcion')
                        ->label('Descripción')
                        ->placeholder('Ejemplo: Habitación con vista al mar y baño privado.')
                        ->maxLength(255)
                        ->validationMessages(['max' => 'La descripción no debe exceder los 255 caracteres.']),
                ]),

            Forms\Components\Section::make('Mantenimiento')
                ->columns(2)
                ->description('Datos sobre la última limpieza y notas adicionales.')
                ->schema([
                    Forms\Components\DateTimePicker::make('ultima_limpieza')
                        ->label('Última Limpieza')
                        ->placeholder('Selecciona la fecha y hora')
                        ->validationMessages(['date' => 'Debe ser una fecha válida.']),

                    Forms\Components\Textarea::make('notas')
                        ->label('Notas Adicionales')
                        ->placeholder('Ejemplo: Falta reponer toallas en el baño.')
                        ->maxLength(255),
                ]),

            Forms\Components\Section::make('Precios y Costos')
                ->columns(3)
                ->description('Configuración de los precios base y adicionales de la habitación.')
                ->schema([
                    Forms\Components\TextInput::make('precio_base')
                        ->label('Precio Base')
                        ->required()
                        ->numeric()
                        ->default(0.00)
                        ->prefix('S/')
                        ->reactive()
                        ->afterStateUpdated(
                            fn(callable $get, callable $set) =>
                            $set('precio_final', (float) $get('precio_base') + (float) $get('precio_caracteristicas'))
                        )
                        ->validationMessages([
                            'required' => 'El precio base es obligatorio.',
                            'numeric' => 'Debe ser un valor numérico válido.',
                        ]),

                    Forms\Components\TextInput::make('precio_caracteristicas')
                        ->label('Costo por Características')
                        ->numeric()
                        ->default(0.00)
                        ->prefix('S/'),

                    Forms\Components\TextInput::make('precio_final')
                        ->label('Precio Final')
                        ->required()
                        ->numeric()
                        ->default(0.00)
                        ->prefix('S/')
                        //->disabled()
                        ->validationMessages([
                            'required' => 'El precio final es obligatorio.',
                            'numeric' => 'Debe ser un valor numérico válido.',
                        ]),
                ]),


        ]);

    }

    protected function beforeSave($record): void
    {
        if (!$record->exists) {
            $record->save(); // Guarda la habitación antes de asignar características
        }
    }
    protected function afterSave($record): void
    {
        if (request()->has('data.caracteristicas')) {
            $record->caracteristicas()->sync(request()->input('data.caracteristicas'));
        }
    }


    public static function table(Table $table): Table
    {
        return $table

            ->columns([

                Tables\Columns\TextColumn::make('numero')
                    ->searchable()
                    ->label('Número')
                    ->size(TextColumn\TextColumnSize::Large),
                //->weight(FontWeight::Bold),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Disponible' => 'success', // Verde para habitaciones listas
                        'Limpiar' => 'warning', // Amarillo para habitaciones que requieren limpieza
                        'Deshabilitada' => 'gray', // Gris para habitaciones fuera de servicio
                        'Mantenimiento' => 'danger', // Rojo para habitaciones en reparación
                        'Ocupada' => 'danger', // Rojo para habitaciones en reparación
                        default => 'secondary', // Color por defecto si hay valores inesperados
                    }),

                Tables\Columns\TextColumn::make('tipo.name')
                    ->sortable(),


                Tables\Columns\TextColumn::make('tipo.capacidad')
                    ->label('Capacidad')
                    ->sortable()
                    ->icon('heroicon-s-user-group'),

                Tables\Columns\TextColumn::make('ubicacion')
                    ->prefix('Piso: ')
                    ->icon('heroicon-s-building-office'),

                Tables\Columns\TextColumn::make('precio_base')
                    ->numeric()
                    ->prefix('S/ ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_final')
                    ->numeric()
                    ->prefix('S/ ')
                    ->sortable(),
                Tables\Columns\TextColumn::make('ultima_limpieza')
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
            'index' => Pages\ListHabitacions::route('/'),
            'create' => Pages\CreateHabitacion::route('/create'),
            'edit' => Pages\EditHabitacion::route('/{record}/edit'),
        ];
    }
}
