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
                            ->live() // Hacer que el campo sea reactivo
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Recalcular precios cuando cambien las características
                                $precioBase = (float) ($get('precio_base') ?? 0);
                                $precioCaracteristicas = 0;

                                if (!empty($state)) {
                                    $caracteristicas = \App\Models\Caracteristica::whereIn('id', $state)->get();
                                    $precioCaracteristicas = $caracteristicas->sum('precio');
                                }

                                $set('precio_caracteristicas', number_format($precioCaracteristicas, 2, '.', ''));
                                $set('precio_final', number_format($precioBase + $precioCaracteristicas, 2, '.', ''));
                            })
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
                            ->live(onBlur: true) // Actualizar cuando pierde el foco
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                // Recalcular precios cuando cambie el precio base
                                $precioBase = (float) ($state ?? 0);
                                $caracteristicasIds = $get('caracteristicas') ?? [];
                                $precioCaracteristicas = 0;

                                if (!empty($caracteristicasIds)) {
                                    $caracteristicas = \App\Models\Caracteristica::whereIn('id', $caracteristicasIds)->get();
                                    $precioCaracteristicas = $caracteristicas->sum('precio');
                                }

                                $set('precio_caracteristicas', number_format($precioCaracteristicas, 2, '.', ''));
                                $set('precio_final', number_format($precioBase + $precioCaracteristicas, 2, '.', ''));
                            })
                            ->validationMessages([
                                'required' => 'Debe ingresar el precio base.',
                                'numeric' => 'El precio debe ser un número válido.',
                                'min' => 'El precio no puede ser negativo.',
                            ]),

                        Forms\Components\TextInput::make('precio_caracteristicas')
                            ->label('Precio de características (S/)')
                            ->placeholder('Se calcula automáticamente')
                            ->numeric()
                            ->readOnly()
                            ->prefix('S/')
                            ->helperText('Este valor se calcula automáticamente basado en las características seleccionadas.')
                            ->extraAttributes(['class' => 'bg-gray-50']),

                        Forms\Components\TextInput::make('precio_final')
                            ->label('Precio final por noche (S/)')
                            ->placeholder('Se calcula automáticamente')
                            ->numeric()
                            ->readOnly()
                            ->prefix('S/')
                            ->helperText('Precio base + precio de características.')
                            ->extraAttributes(['class' => 'bg-gray-50']),

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
                    ->label('Tipo de Habitación')
                    ->extraAttributes(['class' => 'font-bold text-primary-600'])
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('capacidad')
                    ->label('Capacidad')
                    ->sortable()
                    ->icon('heroicon-s-user-group')
                    ->iconColor('primary')
                    ->suffix(' personas')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('precio_base')
                    ->label('Precio Base')
                    ->money('PEN')
                    ->sortable()
                    ->color('success')
                    ->alignment('right'),

                Tables\Columns\TextColumn::make('precio_caracteristicas')
                    ->label('Precio Características')
                    ->money('PEN')
                    ->sortable()
                    ->color('warning')
                    ->alignment('right')
                    ->formatStateUsing(fn($state) => $state == 0 || $state === null ? 'S/ 0.00' : 'S/ ' . number_format($state, 2)),

                Tables\Columns\TextColumn::make('precio_final')
                    ->label('Precio Final/Noche')
                    ->money('PEN')
                    ->sortable()
                    ->color('primary')
                    ->weight('bold')
                    ->alignment('right')
                    ->formatStateUsing(fn($state) => 'S/ ' . number_format($state, 2)),

                Tables\Columns\TextColumn::make('caracteristicas.name')
                    ->label('Características')
                    ->listWithLineBreaks()
                    ->badge()
                    ->limit(3)
                    ->tooltip(function ($record) {
                        $caracteristicas = $record->caracteristicas;
                        if ($caracteristicas->count() <= 3) return null;

                        return $caracteristicas->skip(3)->pluck('name')->join(', ');
                    }),

                Tables\Columns\IconColumn::make('activa')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignment('center'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y H:i')
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
                Tables\Actions\Action::make('recalcular_precios')
                    ->label('Recalcular Precio')
                    ->icon('heroicon-o-calculator')
                    ->color('warning')
                    ->action(function ($record) {
                        \App\Observers\HabitacionTipoObserver::recalcularPreciosManual($record);

                        \Filament\Notifications\Notification::make()
                            ->title('Precio Recalculado')
                            ->body("Precio actualizado para '{$record->name}': S/ {$record->fresh()->precio_final}")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalDescription('Esto recalculará el precio final basado en el precio base más las características seleccionadas.')
                    ->modalSubmitActionLabel('Recalcular'),

                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye')
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil-square')
                    ->color('primary'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash')
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('recalcular_precios_bulk')
                        ->label('Recalcular Precios Seleccionados')
                        ->icon('heroicon-o-calculator')
                        ->color('warning')
                        ->action(function ($records) {
                            $procesados = 0;

                            foreach ($records as $record) {
                                \App\Observers\HabitacionTipoObserver::recalcularPreciosManual($record);
                                $procesados++;
                            }

                            \Filament\Notifications\Notification::make()
                                ->title('Precios Recalculados')
                                ->body("Se recalcularon correctamente {$procesados} tipos de habitación.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalDescription('Esto recalculará los precios finales de todos los tipos seleccionados.')
                        ->modalSubmitActionLabel('Recalcular Todo'),

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
