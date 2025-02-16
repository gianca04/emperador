<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HabitacionTipoResource\Pages;
use App\Models\HabitacionTipo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HabitacionTipoResource extends Resource
{
    protected static ?string $model = HabitacionTipo::class;

    protected static ?string $navigationIcon = 'icon-room';

    protected static ?string $navigationLabel = 'Habitación Tipos';

    protected static ?string $navigationGroup = 'Gestión de Habitaciones';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->columns(2)
                    ->description('Ingrese los detalles básicos del tipo de habitación.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre')
                            ->placeholder('Ejemplo: Habitación Deluxe')
                            ->required()
                            ->maxLength(255)
                            ->validationMessages([
                                'required' => 'El nombre de la habitación es obligatorio.',
                                'max' => 'El nombre no debe superar los 255 caracteres.',
                            ]),

                        Forms\Components\TextInput::make('capacidad')
                            ->label('Capacidad')
                            ->placeholder('Ejemplo: 2')
                            ->required()
                            ->numeric()
                            ->minValue(1)
                            ->prefixIcon('heroicon-s-user-group')
                            ->default(1)
                            ->validationMessages([
                                'required' => 'Debe especificar la capacidad.',
                                'numeric' => 'Debe ingresar un número válido.',
                                'min' => 'La capacidad mínima es 1 persona.',
                            ]),
                    ]),

                Forms\Components\Section::make('Características Adicionales')
                    ->description('Seleccione las características adicionales para este tipo de habitación.')
                    ->schema([
                        Forms\Components\Select::make('caracteristicas')
                            ->label('Características')
                            ->relationship('caracteristicas', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->options(
                                \App\Models\Caracteristica::all()->mapWithKeys(fn($caracteristica) => [
                                    $caracteristica->id => "{$caracteristica->name} - " . ($caracteristica->precio == 0.00 ? "Incluida" : "S/ {$caracteristica->precio}"),
                                ])
                            )
                            ->helperText('Seleccione una o más características disponibles para esta habitación.')
                            ->validationMessages([
                                'exists' => 'Alguna de las características seleccionadas no es válida.',
                            ]),
                    ]),

                Forms\Components\Section::make('Precios y Estado')
                    ->columns(3)
                    ->description('Configura el precio y disponibilidad de la habitación.')
                    ->schema([
                        Forms\Components\TextInput::make('precio_base')
                            ->label('Precio Base (S/)')
                            ->placeholder('Ejemplo: 150.00')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('S/')
                            ->validationMessages([
                                'required' => 'Debe ingresar el precio base.',
                                'numeric' => 'El precio debe ser un número válido.',
                                'min' => 'El precio no puede ser negativo.',
                            ]),

                        Forms\Components\TextInput::make('precio_caracteristicas')
                            ->label('Precio de características (S/)')
                            ->placeholder('Ejemplo: 150.00')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('S/')
                            ->validationMessages([
                                'required' => 'Debe ingresar el precio base.',
                                'numeric' => 'El precio debe ser un número válido.',
                                'min' => 'El precio no puede ser negativo.',
                            ]),

                        Forms\Components\TextInput::make('precio_final')
                            ->label('Precio final por noche. (S/)')
                            ->placeholder('Ejemplo: 150.00')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->prefix('S/')
                            ->validationMessages([
                                'required' => 'Debe ingresar el precio base.',
                                'numeric' => 'El precio debe ser un número válido.',
                                'min' => 'El precio no puede ser negativo.',
                            ]),

                        Forms\Components\Toggle::make('activa')
                            ->label('Disponible')
                            ->default(true)
                            ->required()
                            ->validationMessages([
                                'required' => 'Debe especificar si la habitación está activa o no.',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('capacidad')
                    ->label('Capacidad')
                    ->sortable()
                    ->icon('heroicon-s-user-group'),

                Tables\Columns\TextColumn::make('caracteristicas.name')
                    ->label('Características')
                    ->listWithLineBreaks()
                    ->badge()
                    ->sortable()
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('caracteristicas.precio')
                    ->label(' S/ ')
                    ->money('PEN') // Formatea como moneda (Peruvian Sol)
                    ->sortable()
                    ->listWithLineBreaks()
                    ->badge()
                    ->formatStateUsing(fn($state) => $state == 0 || $state === null ? 'Incluida' : 'S/ ' . number_format($state, 2))
                    ->color(fn($state) => $state == 0 || $state === null ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('precio_caracteristicas')
                    ->sortable()
                    ->label('Costo de Caracteristicas')
                    ->prefix('S/ '),

                Tables\Columns\TextColumn::make('precio_base')
                    ->sortable()
                    ->label('Precio Base')
                    ->prefix('S/ '),


                Tables\Columns\TextColumn::make('precio_final')
                    ->label('Costo Total')
                    ->prefix('S/ ') // Agregar el símbolo de soles
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

                Tables\Filters\TernaryFilter::make('activa')
                    ->label('Estado De Tipo de habitación')
                    ->trueLabel('Activa')
                    ->falseLabel('Inactiva')
                    ->native(false),
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
            'index' => Pages\ListHabitacionTipos::route('/'),
            'create' => Pages\CreateHabitacionTipo::route('/create'),
            'edit' => Pages\EditHabitacionTipo::route('/{record}/edit'),
        ];
    }
}
