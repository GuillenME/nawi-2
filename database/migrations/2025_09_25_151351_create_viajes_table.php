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
        Schema::create('viajes', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->double('lat_salida');
            $table->double('lon_salida');
            $table->double('lat_destino');
            $table->double('lon_destino');
            $table->char('id_taxi', 36);
            $table->char('id_pasajero', 36);
            $table->timestamps();

            // Índices
            $table->index('id_taxi', 'viaje_taxi_idx');
            $table->index('id_pasajero', 'viaje_pasajero_idx');

            // Claves foráneas
            $table->foreign('id_taxi', 'viaje_taxi_fk')
                  ->references('id')
                  ->on('taxis')
                  ->onDelete('cascade');
            $table->foreign('id_pasajero', 'viaje_pasajero_fk')
                  ->references('id')
                  ->on('pasajeros')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};
