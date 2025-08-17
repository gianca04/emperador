<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('dni', 8)->unique()->nullable(); // DNI obligatorio y único
            $table->string('apellido')->nullable(); // Apellido opcional
            $table->string('nombre')->nullable(); // Nombre opcional
            $table->date('nacimiento')->nullable(); // Fecha de nacimiento opcional
            $table->string('telefono', 9)->nullable(); // Teléfono opcional
            $table->string('direccion')->nullable(); // Dirección opcional
            $table->foreignId('distrito_id') // Clave foránea opcional
                ->nullable()
                ->constrained()
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['distrito_id']);
            $table->dropColumn([
                'distrito_id',
                'dni',
                'apellido',
                'nombre',
                'nacimiento',
                'telefono',
                'direccion',
            ]);
        });
    }
}
