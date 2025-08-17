<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Caracteristica;

class CaracteristicaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caracteristicas = [
            ['name' => 'Aire acondicionado', 'precio' => 10, 'activa' => true, 'removible' => false],
            ['name' => 'TV', 'precio' => 0, 'activa' => true, 'removible' => false],
            ['name' => 'SmartTV', 'precio' => 0, 'activa' => true, 'removible' => false],
            ['name' => 'Ducha Caliente', 'precio' => 0, 'activa' => true, 'removible' => false],
            ['name' => 'Frigobar', 'precio' => 0, 'activa' => true, 'removible' => false],
            ['name' => 'Wi-Fi', 'precio' => 0, 'activa' => true, 'removible' => false],
        ];

        foreach ($caracteristicas as $caracteristica) {
            Caracteristica::firstOrCreate(['name' => $caracteristica['name']], $caracteristica);
        }
    }
}
