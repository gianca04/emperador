<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HabitacionResource\Pages;
use App\Filament\Resources\HabitacionResource\RelationManagers;
use App\Models\Habitacion;
use App\Models\Caracteristica;

use App\Models\HabitacionTipo;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HabitacionResource extends Resource
{
    protected static ?string $model = Habitacion::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';


    public static function form(Form $form): Form
    {
        return $form
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
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $set('numero', $state . '00');
                        }
                    }),

                Forms\Components\TextInput::make('numero')
                    ->required()
                    ->numeric()
                    ->label('Número de Habitación')
                    ->unique('habitaciones', 'numero')
                    ->reactive(),

                Forms\Components\Select::make('estado')
                    ->label('Estado Actual de habitación')
                    ->options([
                        'Disponible' => 'Disponible',
                        'Por limpiar' => 'Por limpiar',
                        'Deshabilitada' => 'Deshabilitada',
                        'En Mantenimiento' => 'En Mantenimiento',
                    ])
                    ->searchable()
                    ->preload()
                    ->required()
                    ->default('Disponible'),

                Forms\Components\Textarea::make('descripcion')
                    ->columnSpanFull(),

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
                            $set('precio_caracteristicas', 0);
                            $set('precio_final', $get('precio_base'));
                            return;
                        }

                        // Obtener características del tipo de habitación
                        $tipoHabitacion = HabitacionTipo::find($state);
                        if ($tipoHabitacion) {
                            $caracteristicas = $tipoHabitacion->caracteristicas()->pluck('caracteristicas.id')->toArray();
                            $set('caracteristicas', $caracteristicas);

                            // Sumar los precios de las características
                            $total = Caracteristica::whereIn('id', $caracteristicas)->sum('precio');
                            $set('precio_caracteristicas', $total);
                        } else {
                            $set('caracteristicas', []);
                            $set('precio_caracteristicas', 0);
                        }

                        // Actualizar el precio final
                        $set('precio_final', $get('precio_base') + $get('precio_caracteristicas'));
                    }),

                Forms\Components\Section::make('Características de habitación')
                    ->description('Seleccione las características adicionales para esta habitación.')
                    ->schema([
                        Forms\Components\Select::make('caracteristicas')
                            ->label('Características')
                            ->relationship('caracteristicas', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->reactive()
                            ->options(
                                fn(callable $get) =>
                                Caracteristica::query()
                                    ->select('caracteristicas.id', 'caracteristicas.name', 'caracteristicas.precio')
                                    ->orderBy('caracteristicas.name')
                                    ->get()
                                    ->mapWithKeys(fn($caracteristica) => [
                                        $caracteristica->id => "{$caracteristica->name} - " .
                                            ($caracteristica->precio == 0.00 ? "Incluida" : "S/ {$caracteristica->precio}"),
                                    ])
                            )
                            ->helperText('Seleccione una o más características disponibles para esta habitación.')
                            ->validationMessages([
                                'exists' => 'Alguna de las características seleccionadas no es válida.',
                            ])
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (!$state) {
                                    $set('precio_caracteristicas', 0);
                                } else {
                                    $total = Caracteristica::whereIn('id', $state)->sum('precio');
                                    $set('precio_caracteristicas', $total);
                                }
                            }),
                    ]),

                Forms\Components\Textarea::make('notas')
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('precio_base')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $set('precio_final', $get('precio_base') + $get('precio_caracteristicas'));
                    }),

                Forms\Components\TextInput::make('precio_caracteristicas')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->disabled(), // Evita que el usuario lo edite manualmente

                Forms\Components\TextInput::make('precio_final')
                    ->required()
                    ->numeric()
                    ->default(0.00)
                    ->disabled() // Evita edición manual
                    ->reactive()
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        $set('precio_final', $get('precio_base') + $get('precio_caracteristicas'));
                    }),

                Forms\Components\DateTimePicker::make('ultima_limpieza'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('habitacion_tipo_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('capacidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ubicacion'),
                Tables\Columns\TextColumn::make('precio_base')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_final')
                    ->numeric()
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
