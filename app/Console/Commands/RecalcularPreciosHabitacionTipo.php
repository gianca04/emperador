<?php

namespace App\Console\Commands;

use App\Models\HabitacionTipo;
use App\Observers\HabitacionTipoObserver;
use Illuminate\Console\Command;

class RecalcularPreciosHabitacionTipo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'habitacion:recalcular-precios
                            {--force : Forzar recálculo sin confirmación}
                            {--id= : Recalcular solo un tipo específico por ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalcula los precios finales de todos los tipos de habitación basado en precio base + características';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🏨 Iniciando recálculo de precios de tipos de habitación...');

        // Si se especifica un ID específico
        if ($this->option('id')) {
            return $this->recalcularTipoEspecifico((int) $this->option('id'));
        }

        // Obtener todos los tipos de habitación
        $tiposHabitacion = HabitacionTipo::with('caracteristicas')->get();

        if ($tiposHabitacion->isEmpty()) {
            $this->warn('No se encontraron tipos de habitación para procesar.');
            return 0;
        }

        $this->info("Se encontraron {$tiposHabitacion->count()} tipos de habitación.");

        // Confirmar antes de proceder (a menos que se use --force)
        if (!$this->option('force') && !$this->confirm('¿Desea continuar con el recálculo de precios?')) {
            $this->info('Operación cancelada.');
            return 0;
        }

        // Crear barra de progreso
        $bar = $this->output->createProgressBar($tiposHabitacion->count());
        $bar->start();

        $procesados = 0;
        $errores = 0;

        foreach ($tiposHabitacion as $tipo) {
            try {
                $precioAnterior = $tipo->precio_final;

                // Recalcular usando el observer
                HabitacionTipoObserver::recalcularPreciosManual($tipo);

                $precioNuevo = $tipo->fresh()->precio_final;

                $this->line('');
                $this->info("✅ {$tipo->name}: S/ {$precioAnterior} → S/ {$precioNuevo}");

                $procesados++;

            } catch (\Exception $e) {
                $this->line('');
                $this->error("❌ Error procesando {$tipo->name}: " . $e->getMessage());
                $errores++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->line('');

        // Resumen final
        $this->info("🎉 Proceso completado:");
        $this->info("   ✅ Procesados correctamente: {$procesados}");

        if ($errores > 0) {
            $this->warn("   ❌ Errores encontrados: {$errores}");
        }

        return 0;
    }

    /**
     * Recalcular un tipo específico de habitación
     */
    private function recalcularTipoEspecifico(int $id): int
    {
        $tipo = HabitacionTipo::with('caracteristicas')->find($id);

        if (!$tipo) {
            $this->error("❌ No se encontró un tipo de habitación con ID: {$id}");
            return 1;
        }

        try {
            $precioAnterior = $tipo->precio_final;

            HabitacionTipoObserver::recalcularPreciosManual($tipo);

            $precioNuevo = $tipo->fresh()->precio_final;

            $this->info("✅ Tipo '{$tipo->name}' actualizado:");
            $this->info("   Precio anterior: S/ {$precioAnterior}");
            $this->info("   Precio nuevo: S/ {$precioNuevo}");

            // Mostrar desglose de características
            if ($tipo->caracteristicas->count() > 0) {
                $this->info('   Características incluidas:');
                foreach ($tipo->caracteristicas as $caracteristica) {
                    $this->line("     - {$caracteristica->name}: S/ {$caracteristica->precio}");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Error procesando tipo '{$tipo->name}': " . $e->getMessage());
            return 1;
        }
    }
}
