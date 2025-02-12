<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('organizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('ruc', 11)->unique();
            $table->enum('tipo_ruc', ['10', '20']);
            $table->string('telefono', 20);
            $table->string('email', 100)->unique();
            $table->text('direccion')->nullable();
            $table->string('nombre_contacto', length: 255);
            $table->string('telefono_contacto', 20);
            $table->string('telefono_secundario', 20)->nullable();
            $table->string('email_contacto', 100)->nullable();
            $table->enum('tipo_organizacion', ['EMPRESA', 'ONG', 'GOBIERNO', 'OTRA']);
            $table->timestamp('fecha_registro')->useCurrent();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizaciones');
    }
};
