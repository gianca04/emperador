<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlquilerResource\Pages;
use App\Filament\Resources\AlquilerResource\RelationManagers;
use App\Models\Alquiler;
use App\Models\Habitacion;
use Filament\Forms;
use Filament\Forms\Form;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AlquilerResource extends Resource
{
    protected static ?string $model = Alquiler::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('habitacion_id')
                    ->label('Habitación')
                    ->options(Habitacion::all()->pluck('numero', 'id')->map(fn($name) => (string) $name)->toArray())
                    ->searchable()
                    ->required(),

                Forms\Components\Select::make('tipo_alquiler')
                    ->label('Tipo de Alquiler')
                    ->options([
                        'HORAS' => 'Por Horas',
                        'DIAS' => 'Por Días',
                    ])
                    ->native()
                    ->default('HORAS')
                    ->required(),

                Forms\Components\DateTimePicker::make('fecha_inicio')
                    ->label('Fecha de Inicio')
                    ->required()
                    ->native(false)
                    ->default(now()),

                Forms\Components\DateTimePicker::make('fecha_fin')
                    ->label('Fecha de Fin')
                    ->nullable()
                    ->after('fecha_inicio'),

                Forms\Components\TextInput::make('horas')
                    ->label('Horas')
                    ->numeric()
                    ->minValue(1)
                    ->nullable()
                    ->visible(fn($get) => $get('tipo_alquiler') === 'HORAS'),

                Forms\Components\TextInput::make('monto_total')
                    ->label('Monto Total')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),

                Forms\Components\DateTimePicker::make('checkin_at')
                    ->label('Check-in')
                    ->nullable(),

                Forms\Components\DateTimePicker::make('checkout_at')
                    ->label('Check-out')
                    ->nullable()
                    ->after('checkin_at'),

                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_curso' => 'En Curso',
                        'finalizado' => 'Finalizado',
                    ])
                    ->default('pendiente')
                    ->required()
                    ->native(false),
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
