<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['work', 'pause'])->default('work');
            $table->timestamp('day_in')->nullable();
            $table->timestamp('day_out')->nullable();
            $table->timestamps();
        
            // Definir la clave foránea correctamente
            $table->unsignedBigInteger('calendar_id');
            $table->foreign('calendar_id')->references('id')->on('calendars')->onDelete('cascade');
        });
        
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
