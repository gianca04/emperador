<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CaracteristicaResource\Pages;
use App\Filament\Resources\CaracteristicaResource\RelationManagers;
use App\Models\Caracteristica;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class CaracteristicaResource extends Resource
{

    protected static ?string $model = Caracteristica::class;

    protected static ?string $navigationIcon = 'icon-caracteristicas'; // Usa el icono personalizado
    protected static ?string $navigationGroup = 'Gestión de Habitaciones';

    public static function beforeSave($record, array $data)
    {
        // Validar antes de guardar
        validator($data, Caracteristica::rules($record->id), Caracteristica::messages())->validate();
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->columns(2)
                    ->description('Datos principales de la característica.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre de la característica')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Ej: Aire Acondicionado')
                            ->unique(table: 'caracteristicas', column: 'name', ignoreRecord: true)
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                                'max' => 'El nombre no puede superar los 255 caracteres.',
                                'unique' => 'Esta característica ya existe.',
                            ]),

                        Forms\Components\TextInput::make('precio')
                            ->label('Precio adicional')
                            ->required()
                            ->numeric()
                            ->default(0.00)
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('S/')
                            ->validationMessages([
                                'required' => 'El precio es obligatorio.',
                                'numeric' => 'Debe ingresar un valor numérico.',
                                'min' => 'El precio no puede ser negativo.',
                            ]),
                    ]),

                Forms\Components\Section::make('Configuraciones')
                    ->columns(2)
                    ->description('Define si la característica está activa y si se puede quitar.')
                    ->schema([
                        Forms\Components\Toggle::make('activa')
                            ->label('¿Está activa?')
                            ->required()
                            ->default(true),

                        Forms\Components\Toggle::make('removible')
                            ->label('¿Es removible?')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->searchable()
                    ->extraAttributes(['class' => 'font-bold'])
                    ->sortable(),


                Tables\Columns\TextColumn::make('precio')
                    ->money('PEN') // Formatea como moneda (Peruvian Sol)
                    ->sortable()
                    ->badge()
                    ->formatStateUsing(fn($state) => $state == 0 || $state === null ? 'Incluida' : 'S/ ' . number_format($state, 2))
                    ->color(fn($state) => $state == 0 || $state === null ? 'success' : 'gray'),

                Tables\Columns\IconColumn::make('activa')
                    ->boolean()
                    ->label('Activo'),

                Tables\Columns\IconColumn::make('removible')
                    ->boolean(),

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
                    ->label('Estado De Caracteristica')
                    ->trueLabel('Activa')
                    ->falseLabel('Inactiva')
                    ->native(false),

                Tables\Filters\TernaryFilter::make('removible')
                    ->label('Caracteristica Removible')
                    ->trueLabel(trueLabel: 'Sí')
                    ->falseLabel('No')
                    ->native(false),
            ])

            ->actions([
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
            'index' => Pages\ListCaracteristicas::route('/'),
            'create' => Pages\CreateCaracteristica::route('/create'),
            'edit' => Pages\EditCaracteristica::route('/{record}/edit'),
        ];
    }
}
