<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AsistenciaResource\Pages;
use App\Filament\Resources\AsistenciaResource\RelationManagers;
use App\Models\Asistencia;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsistenciaResource extends Resource
{
    protected static ?string $model = Asistencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('estudiante_id')
                    ->label('Estudiante')
                    ->searchable()
                    ->getSearchResultsUsing(fn(string $query) =>
                        \App\Models\Estudiante::where('dni', 'like', "%{$query}%")->pluck('nombre', 'id'))
                    ->getOptionLabelUsing(fn($value) =>
                        \App\Models\Estudiante::find($value)?->nombre)
                    ->required(),

                Forms\Components\Select::make('estado')
                    ->required()
                    ->options([
                        'tardanza' => 'tardanza',
                        'asistio' => 'asistio',
                        'falto' => 'falto',
                    ])
                ,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('estudiante.nombre')
                    ->label('Estudiante')
                    ->searchable() // Ahora sin la función Closure
                    ->sortable(),

                Tables\Columns\TextColumn::make('estudiante.dni')
                    ->label('Estudiante')
                    ->searchable() // Ahora sin la función Closure
                    ->sortable(),


                Tables\Columns\TextColumn::make('estado'),
                Tables\Columns\TextColumn::make('fecha')
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



                SelectFilter::make('estado')
                    ->label('Estado de Asistencia')
                    ->options([
                        'falto' => 'Faltó',
                        'asistio' => 'Asistió',
                        'tardanza' => 'Tardanza',
                    ])
                    ->attribute('estado'), // Asegúrate de que la columna en la BD sea 'estado'


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
            'index' => Pages\ListAsistencias::route('/'),
            'create' => Pages\CreateAsistencia::route('/create'),
            'edit' => Pages\EditAsistencia::route('/{record}/edit'),
        ];
    }
}
