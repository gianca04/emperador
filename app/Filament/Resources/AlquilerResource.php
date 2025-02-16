<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlquilerResource\Pages;
use Filament\Forms\Components\TextInput;

use App\Filament\Resources\AlquilerResource\RelationManagers;
use App\Models\Alquiler;
use App\Models\Caracteristica;
use App\Models\Habitacion;
use Filament\Forms;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Notifications\Notification;

use Filament\Forms\Components\DateTimePicker;
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
    protected static ?string $navigationLabel = 'Alquiler';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Datos Generales')
                ->columns(2)
                ->description('Información principal sobre el alquiler.')
                ->schema([

                    Forms\Components\Select::make('habitacion_id')
                        ->label('Habitación')
                        ->options(Habitacion::pluck('numero', 'id')->toArray())
                        ->searchable()
                        ->live() // Permite actualización dinámica
                        ->required()
                        ->afterStateUpdated(function ($state, callable $set) {
                            if (!$state) {
                                $set('caracteristicas', []);
                                $set('precio_caracteristicas', 0);
                                return;
                            }

                            // Obtener la habitación con sus características cargadas
                            $habitacion = Habitacion::with('caracteristicas')->find($state);

                            if ($habitacion) {
                                $caracteristicas = $habitacion->caracteristicas->pluck('id')->toArray();
                                $set('caracteristicas', $caracteristicas);

                                // Calcular el precio total de las características
                                $total = $habitacion->caracteristicas->sum('precio');
                                $set('precio_caracteristicas', $total);
                            } else {
                                $set('caracteristicas', []);
                                $set('precio_caracteristicas', 0);
                            }
                        })
                        ->afterStateUpdated(
                            fn($state, callable $set) =>
                            $set('habitacionInfo', Habitacion::find($state)?->toArray() ?? [])
                        )
                        ->validationMessages(['required' => 'Selecciona una habitación.']),

                    Select::make('tipo_alquiler')
                        ->label('Tipo de Alquiler')
                        ->options([
                            'HORAS' => 'Por Horas',
                            'DIAS' => 'Por Días',
                        ])
                        ->default('HORAS')
                        ->required()
                        ->validationMessages([
                            'required' => 'Debe seleccionar el tipo de alquiler.',
                        ]),

                    Section::make('Información de la Habitación')
                        ->collapsed()
                        ->columns(3)
                        ->hidden(fn($get) => empty($get('habitacionInfo'))) // Oculta si no hay información
                        ->schema([
                            TextInput::make('habitacionInfo.numero')
                                ->label('Número de Habitación')
                                ->disabled(),

                            TextInput::make('habitacionInfo.ubicacion')
                                ->label('Ubicación')
                                ->disabled()
                                ->prefix('Piso:'),

                            TextInput::make('habitacionInfo.precio_base')
                                ->label('Precio Base')
                                ->numeric()
                                ->disabled(),

                            TextInput::make('habitacionInfo.precio_final')
                                ->label('Precio Final')
                                ->numeric()
                                ->disabled(),

                            DateTimePicker::make('habitacionInfo.ultima_limpieza')
                                ->label('Última Limpieza')
                                ->disabled(),

                            Forms\Components\Textarea::make('habitacionInfo.descripcion')
                                ->label('Descripción')
                                ->disabled(),

                            Forms\Components\Textarea::make('habitacionInfo.notas')
                                ->label('Notas')
                                ->disabled(),
                        ]),

                ]),

            Section::make('Duración del Alquiler')
                ->description('Defina el período del alquiler, ya sea por horas o días.')
                ->schema([
                    DateTimePicker::make('fecha_inicio')
                        ->label('Fecha de Inicio')
                        ->required()
                        ->native(false)
                        ->default(now())
                        ->validationMessages([
                            'required' => 'La fecha de inicio es obligatoria.',
                        ]),

                    DateTimePicker::make('fecha_fin')
                        ->label('Fecha de Fin')
                        ->nullable()
                        ->after('fecha_inicio')
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


            Forms\Components\Section::make('Detalles de la Habitación')
                ->columns(2)
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
                                $caracteristicasNoRemovibles = $habitacion->caracteristicas->where('removible', false)->pluck('id')->toArray();

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
                ]),

            Section::make('Estado y Control de Check-in/Check-out')
                ->description('Registre la información del check-in y check-out.')
                ->schema([
                    DateTimePicker::make('checkin_at')
                        ->label('Check-in')
                        ->nullable(),

                    DateTimePicker::make('checkout_at')
                        ->label('Check-out')
                        ->nullable()
                        ->after('checkin_at')
                        ->validationMessages([
                            'after' => 'La fecha de check-out debe ser posterior al check-in.',
                        ]),

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
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('tipo_alquiler')
                    ->label('Tipo de Alquiler')
                    ->sortable(),

                \Filament\Tables\Columns\TextColumn::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->sortable()
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
                    ->color(fn(?string $state): string => match ($state) {
                        'FINALIZADO' => 'success',  // Verde
                        'PENDIENTE' => 'warning',   // Amarillo
                        'EN_CURSO' => 'danger',     // Rojo
                        default => 'secondary',     // Gris si el estado no es reconocido
                    }),
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_curso' => 'En Curso',
                        'finalizado' => 'Finalizado',
                    ]),
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
            'index' => Pages\ListAlquilers::route('/'),
            'create' => Pages\CreateAlquiler::route('/create'),
            'edit' => Pages\EditAlquiler::route('/{record}/edit'),
        ];
    }
}
