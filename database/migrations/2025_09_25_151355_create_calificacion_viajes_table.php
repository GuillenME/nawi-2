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
        Schema::create('calificacion_viajes', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->enum('calificacion', ['1', '2', '3', '4', '5']);
            $table->char('id_pasajero', 36);
            $table->char('id_viaje', 36);
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_pasajero', 'calificacion_viaje_pasajero_fk')
                  ->references('id')
                  ->on('pasajeros')
                  ->onDelete('cascade');
            $table->foreign('id_viaje', 'calificacion_viaje_viaje_fk')
                  ->references('id')
                  ->on('viajes')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calificacion_viajes');
    }
};
