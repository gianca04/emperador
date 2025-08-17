<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Habitacion;
use App\Models\HabitacionTipo;

class HabitacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rangos = [
            range(201, 211),
            range(301, 311),
            range(401, 410),
        ];

        $tipos = HabitacionTipo::pluck('id', 'name');
        $matrimonialCA = $tipos['Matrimonial C/A y Frigobar'] ?? $tipos->first();
        $tiposArray = $tipos->toArray();
        $tipoKeys = array_keys($tiposArray);

        foreach ($rangos as $rango) {
            foreach ($rango as $numero) {
                // Las habitaciones x10, x11, x12 son Matrimonial C/A y Frigobar
                $sufijo = substr($numero, -2);
                if (in_array($sufijo, ['10', '11', '12'])) {
                    $tipoId = $matrimonialCA;
                } else {
                    // Selecciona un tipo aleatorio
                    $tipoId = $tiposArray[$tipoKeys[array_rand($tipoKeys)]];
                }

                Habitacion::firstOrCreate(
                    ['numero' => $numero],
                    [
                        'estado' => 'Disponible',
                        'habitacion_tipo_id' => $tipoId,
                        'descripcion' => null,
                        'notas' => null,
                        'ubicacion' => null,
                        'precio_base' => null,
                        'precio_caracteristicas' => null,
                        'precio_final' => null,
                        'ultima_limpieza' => null,
                    ]
                );
            }
        }
    }
}
