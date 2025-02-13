<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlquilerResource\Pages;
use App\Filament\Resources\AlquilerResource\RelationManagers;
use App\Models\Alquiler;
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
                    ->relationship('habitacion', 'id')
                    ->required(),
                Forms\Components\TextInput::make('tipo_alquiler')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_inicio')
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_fin'),
                Forms\Components\TextInput::make('horas')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('monto_total')
                    ->required()
                    ->numeric()
                    ->default(0.00),
                Forms\Components\DateTimePicker::make('checkin_at'),
                Forms\Components\DateTimePicker::make('checkout_at'),
                Forms\Components\TextInput::make('estado')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('habitacion.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tipo_alquiler'),
                Tables\Columns\TextColumn::make('fecha_inicio')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_fin')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('horas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('monto_total')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkin_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('checkout_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('estado'),
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
            'index' => Pages\ListAlquilers::route('/'),
            'create' => Pages\CreateAlquiler::route('/create'),
            'edit' => Pages\EditAlquiler::route('/{record}/edit'),
        ];
    }
}
