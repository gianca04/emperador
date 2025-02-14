<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Shop\Product;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use App\Livewire\ListProducts;

use App\Filament\Resources\HabitacionResource\Pages;
use App\Filament\Resources\HabitacionResource\RelationManagers;
use App\Models\Habitacion;
use App\Models\Caracteristica;
use Filament\Support\Enums\FontWeight;
use App\Models\HabitacionTipo;
use Filament\Forms;
use Illuminate\Validation\Rule;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Route;
use Illuminate\Contracts\View\View;

class Habitaciones extends Component implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;


    public static function table(Table $table): Table
    {
        return $table
            ->query(Habitacion::query())

            ->columns([

                Tables\Columns\Layout\Grid::make()
                    ->columns(1)
                    ->schema([

                        Tables\Columns\TextColumn::make('numero')
                            ->searchable()
                            ->label('Número')
                            ->size(TextColumn\TextColumnSize::Large)
                            ->weight(FontWeight::Bold),

                        Tables\Columns\TextColumn::make('estado')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Disponible' => 'success', // Verde para habitaciones listas
                                'Por limpiar' => 'warning', // Amarillo para habitaciones que requieren limpieza
                                'Deshabilitada' => 'gray', // Gris para habitaciones fuera de servicio
                                'En Mantenimiento' => 'danger', // Rojo para habitaciones en reparación
                                default => 'secondary', // Color por defecto si hay valores inesperados
                            }),

                        Tables\Columns\TextColumn::make('tipo.name')
                            ->sortable(),


                        Tables\Columns\TextColumn::make('tipo.capacidad')
                            ->label('Capacidad')
                            ->sortable()
                            ->icon('heroicon-s-user-group'),

                        Tables\Columns\TextColumn::make('ubicacion')
                            ->prefix('Piso: ')
                            ->icon('heroicon-s-building-office'),

                        Tables\Columns\TextColumn::make('precio_base')
                            ->numeric()
                            ->prefix('S/ ')
                            ->sortable(),
                        Tables\Columns\TextColumn::make('precio_final')
                            ->numeric()
                            ->prefix('S/ ')
                            ->sortable(),
                        Tables\Columns\TextColumn::make('ultima_limpieza')
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
            ])

            ->contentGrid([
                'md' => '3',
                'xl' => 4,
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

    public $count = 1;

    public function increment()
    {
        $this->count++;
    }

    public function decrement()
    {
        $this->count--;
    }

    public function render()
    {
        return view('livewire.habitaciones', [
            'habitaciones' => Habitacion::all(),
        ]);
    }
}
