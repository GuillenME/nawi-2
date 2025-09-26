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
        Schema::create('suscripciones', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->char('id_taxista', 36);
            $table->tinyInteger('pagado');
            $table->double('precio');
            $table->tinyInteger('activo');
            $table->timestamps();

            // Clave foránea
            $table->foreign('id_taxista', 'suscripcion_taxista_fk')
                  ->references('id')
                  ->on('taxistas')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suscripciones');
    }
};
