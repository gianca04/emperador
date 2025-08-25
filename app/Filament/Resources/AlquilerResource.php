<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlquilerResource\Pages;
use Filament\Forms\Components\TextInput;

use App\Filament\Resources\AlquilerResource\RelationManagers;
use App\Models\Alquiler;
use App\Models\Caracteristica;
use App\Models\Habitacion;
use App\Models\HabitacionTipo;
use Carbon\Carbon;
use Filament\Forms;
use Malzariey\FilamentDaterangepickerFilter\Filters\DateRangeFilter;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Support\Enums\IconPosition;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;


use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlquilerResource extends Resource
{
    protected static ?string $model = Alquiler::class;

    protected static ?string $navigationIcon = 'icon-alquiler';
    protected static ?string $navigationLabel = 'Alquileres';

    public static function form(Form $form): Form
    {

        function calcularFechaFin($fechaInicio, $tipoAlquiler)
        {
            if (!$fechaInicio || !$tipoAlquiler) {
                return null;
            }

            $fecha = Carbon::parse($fechaInicio);

            return match ($tipoAlquiler) {
                'HORAS' => $fecha->addHours(2),
                'DIAS' => $fecha->addDay()->setTime(12, 0, 0),
                default => null
            };
        }


        return $form->schema([

            Section::make('Datos Generales')
                ->columns(2)
                ->description('Información principal sobre el alquiler.')
                ->schema([

                    Forms\Components\Toggle::make('mostrar_todas_habitaciones')
                        ->label('Mostrar todas las habitaciones')
                        ->helperText('Por defecto solo se muestran habitaciones disponibles')
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('habitacion_id', null))
                        ->columnSpanFull(),

                    Grid::make(2)
                        ->schema([
                            Select::make('habitacion_id')
                                ->label('Habitación')
                                ->options(function ($get) {
                                    $query = Habitacion::with('tipo')->orderBy('numero');

                                    // Por defecto mostrar solo disponibles, a menos que se especifique mostrar todas
                                    if (!$get('mostrar_todas_habitaciones')) {
                                        $query->where('estado', 'Disponible');
                                    }

                                    return $query->get()
                                        ->mapWithKeys(function ($habitacion) {
                                            $tipoNombre = $habitacion->tipo?->name ?? 'Sin tipo';
                                            $estado = $habitacion->estado;
                                            $precio = number_format((float)$habitacion->precio_final, 2);

                                            // Iconos según el estado
                                            $icono = match($estado) {
                                                'Disponible' => '✅',
                                                'Ocupada' => '🔴',
                                                'Mantenimiento' => '🔧',
                                                'Limpieza' => '🧹',
                                                default => '⚪'
                                            };

                                            // Crear etiqueta informativa con icono
                                            $label = "{$icono} #{$habitacion->numero} - {$tipoNombre} - S/ {$precio}";

                                            return [$habitacion->id => $label];
                                        });
                                })
                                ->searchable()
                                ->placeholder('Selecciona una habitación...')
                                ->helperText('✅ Disponible | 🔴 Ocupada | 🔧 Mantenimiento | 🧹 Limpieza')
                                ->reactive() // o ->live(), según tu versión de Filament
                                ->required()
                                ->columnSpan(1)
                                // Se ejecuta cuando el formulario se "hidrata" con los valores existentes (modo edición):
                                ->afterStateHydrated(function ($state, callable $set) {
                                    if (!$state) {
                                        // Si no hay habitacion_id, reiniciamos
                                        $set('caracteristicas', []);
                                        $set('precio_caracteristicas', 0);
                                        $set('habitacionInfo', []);
                                        return;
                                    }

                                    // Cargar la habitación con sus características
                                    $habitacion = Habitacion::with('caracteristicas')->find($state);

                                    if ($habitacion) {
                                        $set('caracteristicas', $habitacion->caracteristicas->pluck('id')->toArray());
                                        $set('precio_caracteristicas', $habitacion->caracteristicas->sum('precio'));
                                        $set('habitacionInfo', $habitacion->toArray());
                                    } else {
                                        $set('caracteristicas', []);
                                        $set('precio_caracteristicas', 0);
                                        $set('habitacionInfo', []);
                                    }
                                })
                                // Se ejecuta cada vez que el usuario cambia el select manualmente:
                                ->afterStateUpdated(function ($state, callable $set) {
                                    if (!$state) {
                                        // Si no hay habitacion_id, reiniciamos
                                        $set('caracteristicas', []);
                                        $set('precio_caracteristicas', 0);
                                        $set('habitacionInfo', []);
                                        return;
                                    }

                                    // Cargar la habitación con sus características
                                    $habitacion = Habitacion::with('caracteristicas')->find($state);

                                    if ($habitacion) {
                                        $set('caracteristicas', $habitacion->caracteristicas->pluck('id')->toArray());
                                        $set('precio_caracteristicas', $habitacion->caracteristicas->sum('precio'));
                                        $set('habitacionInfo', $habitacion->toArray());
                                    } else {
                                        $set('caracteristicas', []);
                                        $set('precio_caracteristicas', 0);
                                        $set('habitacionInfo', []);
                                    }
                                })
                                ->validationMessages(['required' => 'Selecciona una habitación.'])
                                ->suffixAction(
                                    Action::make('ver_habitacion')
                                        ->icon('heroicon-o-eye')
                                        ->tooltip('Ver detalles de la habitación')
                                        ->color('info')
                                        ->modalHeading('Información Detallada de la Habitación')
                                        ->modalWidth('2xl')
                                        ->modalSubmitAction(false)
                                        ->modalCancelActionLabel('Cerrar')
                                        ->form(function ($get) {
                                            $habitacionId = $get('habitacion_id');
                                            if (!$habitacionId) {
                                                return [];
                                            }

                                            $habitacion = Habitacion::with(['tipo', 'caracteristicas'])->find($habitacionId);
                                            if (!$habitacion) {
                                                return [];
                                            }

                                            return [
                                                Section::make("Habitación #{$habitacion->numero}")
                                                    ->description("Información completa y actualizada")
                                                    ->icon('heroicon-o-home')
                                                    ->columns(2)
                                                    ->schema([
                                                        Placeholder::make('numero')
                                                            ->label('Número')
                                                            ->content($habitacion->numero),

                                                        Placeholder::make('tipo')
                                                            ->label('Tipo de Habitación')
                                                            ->content($habitacion->tipo?->name ?? 'No definido'),

                                                        Placeholder::make('ubicacion')
                                                            ->label('Ubicación')
                                                            ->content("Piso {$habitacion->ubicacion}"),

                                                        Placeholder::make('estado')
                                                            ->label('Estado Actual')
                                                            ->content($habitacion->estado),

                                                        Placeholder::make('precio_base')
                                                            ->label('Precio Base')
                                                            ->content('S/ ' . number_format((float)$habitacion->precio_base, 2)),

                                                        Placeholder::make('precio_final')
                                                            ->label('Precio Final')
                                                            ->content('S/ ' . number_format((float)$habitacion->precio_final, 2)),

                                                        Placeholder::make('ultima_limpieza')
                                                            ->label('Última Limpieza')
                                                            ->content($habitacion->ultima_limpieza ?
                                                                $habitacion->ultima_limpieza->format('d/m/Y H:i') :
                                                                'No registrada')
                                                            ->columnSpanFull(),
                                                    ]),

                                                Section::make('Características Incluidas')
                                                    ->description('Servicios y comodidades de la habitación')
                                                    ->icon('heroicon-o-star')
                                                    ->hidden($habitacion->caracteristicas->count() === 0)
                                                    ->schema([
                                                        ViewField::make('caracteristicas')
                                                            ->view('filament.components.caracteristicas-lista', [
                                                                'caracteristicas' => $habitacion->caracteristicas,
                                                                'total_caracteristicas' => $habitacion->caracteristicas->sum('precio')
                                                            ])
                                                    ]),

                                                Section::make('Información Adicional')
                                                    ->description('Detalles complementarios')
                                                    ->icon('heroicon-o-information-circle')
                                                    ->hidden(empty($habitacion->descripcion) && empty($habitacion->notas))
                                                    ->schema([
                                                        Placeholder::make('descripcion')
                                                            ->label('Descripción')
                                                            ->content($habitacion->descripcion ?: 'Sin descripción')
                                                            ->hidden(empty($habitacion->descripcion)),

                                                        Placeholder::make('notas')
                                                            ->label('Notas')
                                                            ->content($habitacion->notas ?: 'Sin notas')
                                                            ->hidden(empty($habitacion->notas)),
                                                    ])
                                            ];
                                        })
                                        ->visible(fn ($get) => !empty($get('habitacion_id')))
                                ),
                        ]),
                ]),

            Forms\Components\Section::make('Detalles de la Habitación')

                ->description('Características de la habitación.')
                ->schema([

                    Forms\Components\Select::make('caracteristicas')
                        ->label('Características')
                        ->relationship('caracteristicas', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->options(
                            fn() => Caracteristica::query()
                                ->select('id', 'name', 'precio', 'removible')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn($caracteristica) => [
                                    $caracteristica->id => "{$caracteristica->name} - " .
                                        ($caracteristica->precio == 0.00 ? "Incluida" : "S/ {$caracteristica->precio}") .
                                        ($caracteristica->removible ? "" : " (No removible)")
                                ])
                        )
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            $habitacionId = $get('habitacion_id');
                            if (!$habitacionId) {
                                $set('caracteristicas', []);
                                return;
                            }

                            $habitacion = Habitacion::with('caracteristicas')->find($habitacionId);

                            if ($habitacion) {
                                // Si existen características fijas, se vuelven a agregar en caso de intentar removerlas
                                $caracteristicasNoRemovibles = $habitacion->caracteristicas
                                    ->where('removible', false)
                                    ->pluck('id')
                                    ->toArray();

                                // Restaurar características fijas eliminadas
                                $caracteristicasActuales = $state ?? [];
                                $caracteristicasFinales = array_unique(array_merge($caracteristicasActuales, $caracteristicasNoRemovibles));

                                if ($caracteristicasActuales !== $caracteristicasFinales) {
                                    $set('caracteristicas', $caracteristicasFinales);
                                    Notification::make()
                                        ->title('No puedes remover características fijas.')
                                        ->danger()
                                        ->send();
                                }

                                // Calcular precio actualizado
                                $totalPrecio = Caracteristica::whereIn('id', $caracteristicasFinales)->sum('precio');
                                $set('precio_caracteristicas', (float) $totalPrecio);
                            }
                        })
                        ->validationMessages(['array' => 'Las características deben ser una lista válida.']),
                    TextInput::make('precio_caracteristicas')
                        ->label('Costo Total de las Características')
                        ->disabled() // Campo no editable
                        ->prefix('S/')
                        ->numeric()
                        ->afterStateHydrated(function ($state, callable $set, callable $get) {
                            // Al hidratar, sumar los precios de las características ya seleccionadas
                            $caracteristicas = $get('caracteristicas') ?? [];
                            $total = Caracteristica::whereIn('id', $caracteristicas)->sum('precio');
                            $set('precio_caracteristicas', (float) $total);
                        }),

                ]),


            Section::make('Duración del Alquiler')
                ->columns(3)
                ->description('Defina el período del alquiler, ya sea por horas o días.')
                ->schema([

                    Select::make('tipo_alquiler')
                        ->label('Tipo de Alquiler')
                        ->options([
                            'HORAS' => 'Por Horas',
                            'DIAS' => 'Por Días',
                        ])
                        ->required()
                        ->native(false)
                        ->reactive()
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            // Si existe fecha_inicio, la usamos como base; sino, usamos la hora actual
                            $fechaInicio = $get('fecha_inicio')
                                ? Carbon::parse($get('fecha_inicio'))
                                : Carbon::now();

                            if ($state === 'HORAS') {
                                // Sumar 2 horas a la fecha de inicio (o a la hora actual)
                                $nuevaFechaFin = $fechaInicio->copy()->addHours(2);
                            } elseif ($state === 'DIAS') {
                                // Ubicar al mediodía del día siguiente
                                $nuevaFechaFin = $fechaInicio->copy()->addDay()->setTime(12, 0, 0);
                            } else {
                                $nuevaFechaFin = null;
                            }

                            // Convertir a string para asegurarnos del formato
                            $set('fecha_fin', $nuevaFechaFin ? $nuevaFechaFin->toDateTimeString() : null);
                        })
                        ->validationMessages([
                            'required' => 'Debe seleccionar el tipo de alquiler.',
                        ]),

                    // Campo para la fecha de inicio
                    // Campo para la fecha de inicio
                    DateTimePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->required()
                        ->default(Carbon::now()->toDateTimeString())
                        ->reactive()
                        ->validationMessages([
                            'required' => 'La fecha de inicio es obligatoria.',
                        ]),


                    // Campo para la fecha de fin (se actualizará desde el select)
                    DateTimePicker::make('fecha_fin')
                        ->label('Fecha de Fin')
                        ->nullable()
                        ->reactive()
                        ->validationMessages([
                            'after' => 'La fecha de fin debe ser posterior a la fecha de inicio.',
                        ]),

                    TextInput::make('horas')
                        ->label('Cantidad de Horas')
                        ->numeric()
                        ->minValue(1)
                        ->nullable()
                        ->visible(fn($get) => $get('tipo_alquiler') === 'HORAS')
                        ->placeholder('Ingrese el número de horas')
                        ->validationMessages([
                            'numeric' => 'Debe ingresar un número válido.',
                            'min' => 'Debe ingresar al menos una hora.',
                        ]),
                ]),

            Section::make('Estado y Control de Check-in/Check-out')
                ->description('Registre la información del check-in y check-out.')
                ->columns(3)
                ->schema([

                    // Campo para la fecha de check-in
                    DateTimePicker::make('checkin_at')
                        ->label('Check-in')
                        ->nullable()
                        ->reactive() // Permite reaccionar a cambios en su valor
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            // Si se registra una fecha de check-in, se actualiza el estado a "en_curso".
                            // Si se elimina la fecha de check-in, se restablece el estado a "pendiente" y se limpia el check-out.
                            $set('estado', $state ? 'en_curso' : 'pendiente');
                            if (!$state) {
                                $set('checkout_at', null);
                            }
                        }),

                    // Campo para la fecha de check-out
                    DateTimePicker::make('checkout_at')
                        ->label('Check-out')
                        ->nullable()
                        ->reactive() // Permite reaccionar a cambios en su valor
                        // Se deshabilita si no hay fecha de check-in
                        ->disabled(fn($get) => !$get('checkin_at'))
                        ->afterStateUpdated(function ($state, callable $set, $get) {
                            // Si se registra una fecha de check-out y existe un check-in,
                            // se actualiza el estado a "finalizado".
                            if ($state && $get('checkin_at')) {
                                $set('estado', 'finalizado');
                            }
                        })
                        ->validationMessages([
                            'after' => 'La fecha de check-out debe ser posterior al check-in.',
                        ]),

                    // Campo de estado del alquiler, mostrado en el formulario pero deshabilitado para evitar cambios manuales.
                    Select::make('estado')
                        ->label('Estado del Alquiler')
                        ->options([
                            'pendiente' => 'Pendiente',
                            'en_curso' => 'En Curso',
                            'finalizado' => 'Finalizado',
                        ])
                        ->default('pendiente')
                        ->required()
                        ->native(false)
                        ->disabled(true) // Se deshabilita para que el usuario no lo modifique manualmente
                        ->validationMessages([
                            'required' => 'Debe seleccionar el estado del alquiler.',
                        ]),
                ]),

            Section::make('Costo del Alquiler')
                ->description('Defina el monto total a cobrar por el alquiler.')
                ->schema([
                    TextInput::make('monto_total')
                        ->label('Monto Total')
                        ->numeric()
                        ->minValue(0)
                        ->default(0)
                        ->prefix('$')
                        ->required()
                        ->validationMessages([
                            'required' => 'El monto total es obligatorio.',
                            'numeric' => 'Debe ser un valor numérico.',
                            'min' => 'El monto no puede ser negativo.',
                        ]),
                ]),
        ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('habitacion.nombre')
                    ->label('Habitación')
                    ->sortable()
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('tipo_alquiler')
                    ->label('Tipo de Alquiler')
                    ->sortable()
                    ->searchable(),

                \Filament\Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->sortable()
                    ->searchable()
                    ->dateTime('d/m/Y H:i'),

                \Filament\Tables\Columns\TextColumn::make('fecha_fin')
                    ->label('Fecha de Fin')
                    ->sortable()
                    ->dateTime('d/m/Y H:i'),

                \Filament\Tables\Columns\TextColumn::make('monto_total')
                    ->label('Monto Total')
                    ->sortable()
                    ->money('USD'),

                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'PENDIENTE' => 'Pendiente',
                        'EN_CURSO' => 'En Curso',
                        'FINALIZADO' => 'Finalizado',
                        default => 'Desconocido',
                    })
                    ,
            ])
            ->filters([
                DateRangeFilter::make('created_at'),
                \Filament\Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_curso' => 'En Curso',
                        'finalizado' => 'Finalizado',
                    ])
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
            'index' => Pages\ListAlquilers::route('/'),
            'create' => Pages\CreateAlquiler::route('/create'),
            'edit' => Pages\EditAlquiler::route('/{record}/edit'),
        ];
    }
}
