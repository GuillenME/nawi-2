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
        Schema::create('taxistas', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->char('id_matricula', 36);
            $table->char('id_licencia', 36);
            $table->char('id_usuario', 36);
            $table->timestamps();

            // Índices
            $table->index('id_matricula', 'taxista_matricula_idx');
            $table->index('id_licencia', 'taxista_licencia_idx');
            $table->index('id_usuario', 'taxista_usuario_idx');

            // Claves foráneas
            $table->foreign('id_matricula', 'taxista_matricula_fk')
                  ->references('id')
                  ->on('matriculas')
                  ->onDelete('cascade');
            $table->foreign('id_licencia', 'taxista_licencia_fk')
                  ->references('id')
                  ->on('licencias')
                  ->onDelete('cascade');
            $table->foreign('id_usuario', 'taxista_usuario_fk')
                  ->references('id')
                  ->on('usuarios')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('taxistas');
    }
};
