<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrecioResource\Pages;
use App\Filament\Resources\PrecioResource\RelationManagers;
use App\Models\Precio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PrecioResource extends Resource
{
    protected static ?string $model = Precio::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('precio_por_hora')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('precio_por_mora')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('precio_hora_adicional')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('precio_por_hora')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_por_mora')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('precio_hora_adicional')
                    ->numeric()
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
            'index' => Pages\ListPrecios::route('/'),
            'create' => Pages\CreatePrecio::route('/create'),
            'edit' => Pages\EditPrecio::route('/{record}/edit'),
        ];
    }
}
