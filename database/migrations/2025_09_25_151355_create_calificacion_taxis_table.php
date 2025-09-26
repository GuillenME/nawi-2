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
        Schema::create('calificacion_taxis', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->enum('calificacion', ['1', '2', '3', '4', '5']);
            $table->char('id_taxista', 36);
            $table->char('id_pasajero', 36);
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_taxista', 'calificacion_taxi_taxista_fk')
                  ->references('id')
                  ->on('taxistas')
                  ->onDelete('cascade');
            $table->foreign('id_pasajero', 'calificacion_taxi_pasajero_fk')
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
        Schema::dropIfExists('calificacion_taxis');
    }
};
