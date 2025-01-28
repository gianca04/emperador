<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('provincia_id')->nullable()->after('distrito_id');
            $table->unsignedBigInteger('departamento_id')->nullable()->after('provincia_id');

            // Foreign keys con protección contra eliminación
            $table->foreign('provincia_id')
                ->references('id')->on('provincias')
                ->onDelete('set null'); // Si se borra una provincia, el campo se establece en NULL

            $table->foreign('departamento_id')
                ->references('id')->on('departamentos')
                ->onDelete('set null'); // Si se borra un departamento, el campo se establece en NULL
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['provincia_id']);
            $table->dropForeign(['departamento_id']);
            $table->dropColumn(['provincia_id', 'departamento_id']);
        });
    }

};
