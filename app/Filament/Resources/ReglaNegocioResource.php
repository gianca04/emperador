<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReglaNegocioResource\Pages;
use App\Models\ReglaNegocio;
use App\Models\HabitacionTipo;
use App\Models\Habitacion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReglaNegocioResource extends Resource
{
    protected static ?string $model = ReglaNegocio::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Reglas de Negocio';

    protected static ?string $navigationGroup = 'Configuración del Sistema';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Configuración básica de la regla de negocio')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('nombre')
                                    ->label('Nombre de la Regla')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Ej: Alquiler por horas fin de semana')
                                    ->columnSpan(1),

                                Forms\Components\Select::make('tipo')
                                    ->label('Tipo de Regla')
                                    ->required()
                                    ->options([
                                        ReglaNegocio::TIPO_ALQUILER_HORAS => 'Alquiler por Horas',
                                        ReglaNegocio::TIPO_PENALIZACION_CHECKOUT => 'Penalización de Checkout',
                                        ReglaNegocio::TIPO_DESCUENTO => 'Descuento',
                                        ReglaNegocio::TIPO_RECARGO => 'Recargo',
                                    ])
                                    ->live()
                                    ->columnSpan(1),
                            ]),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada de la regla y cuándo aplica'),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('activa')
                                    ->label('Activa')
                                    ->default(true)
                                    ->helperText('Solo las reglas activas se aplicarán en el sistema'),

                                Forms\Components\TextInput::make('prioridad')
                                    ->label('Prioridad')
                                    ->numeric()
                                    ->default(1)
                                    ->minValue(1)
                                    ->helperText('Mayor número = mayor prioridad'),

                                Forms\Components\Select::make('aplicabilidad_habitaciones')
                                    ->label('Aplicar a')
                                    ->required()
                                    ->options([
                                        ReglaNegocio::APLICABILIDAD_TODAS => 'Todas las habitaciones',
                                        ReglaNegocio::APLICABILIDAD_TIPOS_ESPECIFICOS => 'Tipos específicos',
                                        ReglaNegocio::APLICABILIDAD_HABITACIONES_ESPECIFICAS => 'Habitaciones específicas',
                                    ])
                                    ->live(),
                            ]),
                    ]),

                // Sección de configuración de alquiler por horas
                Forms\Components\Section::make('Configuración de Alquiler por Horas')
                    ->description('Definir precios y rangos para alquiler por horas')
                    ->schema([
                        Forms\Components\Grid::make(4)
                            ->schema([
                                Forms\Components\TextInput::make('horas_minimas')
                                    ->label('Horas Mínimas')
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('Ej: 2')
                                    ->helperText('Mínimo de horas para aplicar esta regla'),

                                Forms\Components\TextInput::make('horas_maximas')
                                    ->label('Horas Máximas')
                                    ->numeric()
                                    ->minValue(1)
                                    ->placeholder('Ej: 12')
                                    ->helperText('Máximo de horas para aplicar esta regla'),

                                Forms\Components\TextInput::make('precio_por_hora')
                                    ->label('Precio por Hora (S/)')
                                    ->numeric()
                                    ->prefix('S/')
                                    ->placeholder('0.00')
                                    ->helperText('Precio por cada hora de alquiler'),

                                Forms\Components\TextInput::make('precio_fijo')
                                    ->label('Precio Fijo (S/)')
                                    ->numeric()
                                    ->prefix('S/')
                                    ->placeholder('0.00')
                                    ->helperText('Precio fijo independiente de las horas'),
                            ]),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('tipo') === ReglaNegocio::TIPO_ALQUILER_HORAS),

                // Sección de configuración de penalización
                Forms\Components\Section::make('Configuración de Penalización de Checkout')
                    ->description('Configurar penalizaciones por checkout tardío')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TimePicker::make('hora_checkout_limite')
                                    ->label('Hora Límite de Checkout')
                                    ->default('12:00')
                                    ->seconds(false)
                                    ->helperText('Hora máxima para hacer checkout sin penalización'),

                                Forms\Components\Select::make('penalizacion_tipo')
                                    ->label('Tipo de Penalización')
                                    ->options([
                                        ReglaNegocio::PENALIZACION_FIJO => 'Monto Fijo',
                                        ReglaNegocio::PENALIZACION_PORCENTAJE => 'Porcentaje del Total',
                                        ReglaNegocio::PENALIZACION_POR_HORA => 'Por Hora de Retraso',
                                    ])
                                    ->default(ReglaNegocio::PENALIZACION_FIJO)
                                    ->live(),

                                Forms\Components\TextInput::make('penalizacion_monto')
                                    ->label(function (Forms\Get $get) {
                                        return match ($get('penalizacion_tipo')) {
                                            ReglaNegocio::PENALIZACION_PORCENTAJE => 'Porcentaje (%)',
                                            ReglaNegocio::PENALIZACION_POR_HORA => 'Monto por Hora (S/)',
                                            default => 'Monto Fijo (S/)',
                                        };
                                    })
                                    ->numeric()
                                    ->prefix(function (Forms\Get $get) {
                                        return $get('penalizacion_tipo') === ReglaNegocio::PENALIZACION_PORCENTAJE ? '%' : 'S/';
                                    })
                                    ->placeholder('0.00'),
                            ]),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('tipo') === ReglaNegocio::TIPO_PENALIZACION_CHECKOUT),

                // Sección de aplicabilidad específica
                Forms\Components\Section::make('Habitaciones Aplicables')
                    ->description('Seleccionar a qué habitaciones o tipos aplica esta regla')
                    ->schema([
                        Forms\Components\Select::make('tiposHabitacion')
                            ->label('Tipos de Habitación')
                            ->relationship('tiposHabitacion', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->helperText('Seleccionar los tipos de habitación donde aplica esta regla'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('aplicabilidad_habitaciones') === ReglaNegocio::APLICABILIDAD_TIPOS_ESPECIFICOS),

                Forms\Components\Section::make('Habitaciones Específicas')
                    ->description('Seleccionar habitaciones específicas donde aplica esta regla')
                    ->schema([
                        Forms\Components\Select::make('habitaciones')
                            ->label('Habitaciones')
                            ->relationship('habitaciones', 'numero')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->getOptionLabelFromRecordUsing(fn (Habitacion $record) => "Habitación {$record->numero} - {$record->tipo?->name}")
                            ->helperText('Seleccionar las habitaciones específicas donde aplica esta regla'),
                    ])
                    ->visible(fn (Forms\Get $get) => $get('aplicabilidad_habitaciones') === ReglaNegocio::APLICABILIDAD_HABITACIONES_ESPECIFICAS),

                // Sección de configuración temporal
                Forms\Components\Section::make('Configuración Temporal')
                    ->description('Definir cuándo aplica esta regla en el tiempo')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha_inicio')
                                    ->label('Fecha de Inicio')
                                    ->helperText('Fecha desde cuando aplica la regla (opcional)'),

                                Forms\Components\DatePicker::make('fecha_fin')
                                    ->label('Fecha de Fin')
                                    ->helperText('Fecha hasta cuando aplica la regla (opcional)')
                                    ->after('fecha_inicio'),
                            ]),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TimePicker::make('hora_inicio')
                                    ->label('Hora de Inicio')
                                    ->seconds(false)
                                    ->helperText('Hora del día desde cuando aplica (opcional)'),

                                Forms\Components\TimePicker::make('hora_fin')
                                    ->label('Hora de Fin')
                                    ->seconds(false)
                                    ->helperText('Hora del día hasta cuando aplica (opcional)')
                                    ->after('hora_inicio'),
                            ]),

                        Forms\Components\CheckboxList::make('dias_semana')
                            ->label('Días de la Semana')
                            ->options([
                                1 => 'Lunes',
                                2 => 'Martes',
                                3 => 'Miércoles',
                                4 => 'Jueves',
                                5 => 'Viernes',
                                6 => 'Sábado',
                                0 => 'Domingo',
                            ])
                            ->columns(4)
                            ->helperText('Días de la semana cuando aplica la regla (opcional, vacío = todos los días)'),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Toggle::make('es_temporada_alta')
                                    ->label('Solo Temporada Alta')
                                    ->helperText('Aplicar solo en temporada alta'),

                                Forms\Components\Toggle::make('es_fin_semana')
                                    ->label('Solo Fines de Semana')
                                    ->helperText('Aplicar solo en fines de semana'),

                                Forms\Components\Toggle::make('es_feriado')
                                    ->label('Solo Feriados')
                                    ->helperText('Aplicar solo en días feriados'),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('tipo')
                    ->label('Tipo')
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            ReglaNegocio::TIPO_ALQUILER_HORAS => 'Alquiler por Horas',
                            ReglaNegocio::TIPO_PENALIZACION_CHECKOUT => 'Penalización Checkout',
                            ReglaNegocio::TIPO_DESCUENTO => 'Descuento',
                            ReglaNegocio::TIPO_RECARGO => 'Recargo',
                            default => $state,
                        };
                    })
                    ->color(function (string $state): string {
                        return match ($state) {
                            ReglaNegocio::TIPO_ALQUILER_HORAS => 'primary',
                            ReglaNegocio::TIPO_PENALIZACION_CHECKOUT => 'warning',
                            ReglaNegocio::TIPO_DESCUENTO => 'success',
                            ReglaNegocio::TIPO_RECARGO => 'danger',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\IconColumn::make('activa')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('aplicabilidad_habitaciones')
                    ->label('Aplicabilidad')
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            ReglaNegocio::APLICABILIDAD_TODAS => 'Todas',
                            ReglaNegocio::APLICABILIDAD_TIPOS_ESPECIFICOS => 'Tipos Específicos',
                            ReglaNegocio::APLICABILIDAD_HABITACIONES_ESPECIFICAS => 'Habitaciones Específicas',
                            default => $state,
                        };
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('precio_por_hora')
                    ->label('Precio/Hora')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable()
                    ->visible(fn ($record) => $record?->tipo === ReglaNegocio::TIPO_ALQUILER_HORAS),

                Tables\Columns\TextColumn::make('precio_fijo')
                    ->label('Precio Fijo')
                    ->money('PEN')
                    ->sortable()
                    ->toggleable()
                    ->visible(fn ($record) => $record?->tipo === ReglaNegocio::TIPO_ALQUILER_HORAS),

                Tables\Columns\TextColumn::make('horas_minimas')
                    ->label('Horas Min.')
                    ->sortable()
                    ->suffix(' h')
                    ->toggleable()
                    ->visible(fn ($record) => $record?->tipo === ReglaNegocio::TIPO_ALQUILER_HORAS),

                Tables\Columns\TextColumn::make('horas_maximas')
                    ->label('Horas Max.')
                    ->sortable()
                    ->suffix(' h')
                    ->toggleable()
                    ->visible(fn ($record) => $record?->tipo === ReglaNegocio::TIPO_ALQUILER_HORAS),

                Tables\Columns\TextColumn::make('hora_checkout_limite')
                    ->label('Checkout Límite')
                    ->time('H:i')
                    ->toggleable()
                    ->visible(fn ($record) => $record?->tipo === ReglaNegocio::TIPO_PENALIZACION_CHECKOUT),

                Tables\Columns\TextColumn::make('penalizacion_monto')
                    ->label('Penalización')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '-';

                        return match ($record->penalizacion_tipo) {
                            ReglaNegocio::PENALIZACION_PORCENTAJE => $state . '%',
                            default => 'S/ ' . number_format($state, 2),
                        };
                    })
                    ->toggleable()
                    ->visible(fn ($record) => $record?->tipo === ReglaNegocio::TIPO_PENALIZACION_CHECKOUT),

                Tables\Columns\TextColumn::make('prioridad')
                    ->label('Prioridad')
                    ->sortable()
                    ->badge()
                    ->color(function (int $state): string {
                        return match (true) {
                            $state >= 10 => 'danger',
                            $state >= 5 => 'warning',
                            default => 'gray',
                        };
                    }),

                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Inicio')
                    ->date('d/m/Y')
                    ->placeholder('Sin límite')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->placeholder('Sin límite')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tiposHabitacion.name')
                    ->label('Tipos de Habitación')
                    ->listWithLineBreaks()
                    ->badge()
                    ->limit(2)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo de Regla')
                    ->options([
                        ReglaNegocio::TIPO_ALQUILER_HORAS => 'Alquiler por Horas',
                        ReglaNegocio::TIPO_PENALIZACION_CHECKOUT => 'Penalización Checkout',
                        ReglaNegocio::TIPO_DESCUENTO => 'Descuento',
                        ReglaNegocio::TIPO_RECARGO => 'Recargo',
                    ]),

                Tables\Filters\TernaryFilter::make('activa')
                    ->label('Estado')
                    ->trueLabel('Solo Activas')
                    ->falseLabel('Solo Inactivas')
                    ->placeholder('Todas'),

                Tables\Filters\SelectFilter::make('aplicabilidad_habitaciones')
                    ->label('Aplicabilidad')
                    ->options([
                        ReglaNegocio::APLICABILIDAD_TODAS => 'Todas las habitaciones',
                        ReglaNegocio::APLICABILIDAD_TIPOS_ESPECIFICOS => 'Tipos específicos',
                        ReglaNegocio::APLICABILIDAD_HABITACIONES_ESPECIFICAS => 'Habitaciones específicas',
                    ]),

                Tables\Filters\Filter::make('vigente')
                    ->label('Vigentes Hoy')
                    ->query(function (Builder $query): Builder {
                        $hoy = now()->format('Y-m-d');
                        return $query->where(function ($q) use ($hoy) {
                            $q->whereNull('fecha_inicio')
                              ->orWhere('fecha_inicio', '<=', $hoy);
                        })->where(function ($q) use ($hoy) {
                            $q->whereNull('fecha_fin')
                              ->orWhere('fecha_fin', '>=', $hoy);
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('duplicar')
                    ->label('Duplicar')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    ->action(function (ReglaNegocio $record) {
                        $nuevoRecord = $record->replicate();
                        $nuevoRecord->nombre = $record->nombre . ' (Copia)';
                        $nuevoRecord->activa = false;
                        $nuevoRecord->save();

                        // Copiar relaciones many-to-many
                        if ($record->tiposHabitacion()->exists()) {
                            $nuevoRecord->tiposHabitacion()->attach(
                                $record->tiposHabitacion()->pluck('habitacion_tipos.id')->toArray()
                            );
                        }

                        if ($record->habitaciones()->exists()) {
                            $nuevoRecord->habitaciones()->attach(
                                $record->habitaciones()->pluck('habitaciones.id')->toArray()
                            );
                        }

                        \Filament\Notifications\Notification::make()
                            ->title('Regla Duplicada')
                            ->body("Se creó una copia de '{$record->nombre}' como '{$nuevoRecord->nombre}'")
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation(),

                Tables\Actions\ViewAction::make()
                    ->color('info'),

                Tables\Actions\EditAction::make()
                    ->color('primary'),

                Tables\Actions\DeleteAction::make()
                    ->color('danger'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activar')
                        ->label('Activar Seleccionadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['activa' => true]);

                            \Filament\Notifications\Notification::make()
                                ->title('Reglas Activadas')
                                ->body('Se activaron ' . $records->count() . ' reglas seleccionadas')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('desactivar')
                        ->label('Desactivar Seleccionadas')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->action(function ($records) {
                            $records->each->update(['activa' => false]);

                            \Filament\Notifications\Notification::make()
                                ->title('Reglas Desactivadas')
                                ->body('Se desactivaron ' . $records->count() . ' reglas seleccionadas')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('prioridad', 'desc');
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
            'index' => Pages\ListReglaNegocios::route('/'),
            'create' => Pages\CreateReglaNegocio::route('/create'),
            'edit' => Pages\EditReglaNegocio::route('/{record}/edit'),
        ];
    }
}
