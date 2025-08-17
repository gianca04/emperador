<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HabitacionTipo;

class HabitacionTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tipos = [
            ['name' => 'Matrimonial Simple', 'precio_base' => 80, 'activa' => true, 'capacidad' => 2],
            ['name' => 'Simple', 'precio_base' => 60, 'activa' => true, 'capacidad' => 1],
            ['name' => 'Doble', 'precio_base' => 80, 'activa' => true, 'capacidad' => 2],
            ['name' => 'Familiar', 'precio_base' => 100, 'activa' => true, 'capacidad' => 4],
            ['name' => 'Simple C/A', 'precio_base' => 80, 'activa' => true, 'capacidad' => 1],
            ['name' => 'Matrimonial C/A y Frigobar', 'precio_base' => 100, 'activa' => true, 'capacidad' => 2],
        ];

        foreach ($tipos as $tipo) {
            HabitacionTipo::firstOrCreate(['name' => $tipo['name']], $tipo);
        }
    }
}
