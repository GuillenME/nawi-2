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
        Schema::create('matriculas', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->longText('url');
            $table->dateTime('fecha_subida');
            $table->char('id_estatus', 36);
            $table->timestamps();

            // Índices
            $table->index('id_estatus', 'matricula_estatus_idx');

            // Clave foránea
            $table->foreign('id_estatus', 'matricula_estatus_fk')
                  ->references('id')
                  ->on('estatus_documentos')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matriculas');
    }
};
