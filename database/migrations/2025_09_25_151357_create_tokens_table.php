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
        Schema::create('tokens', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('token', 255);
            $table->char('id_usuario', 36);
            $table->dateTime('fecha_creacion');
            $table->dateTime('fecha_expiracion');
            $table->tinyInteger('expirado');
            $table->timestamps();

            // Índices
            $table->index('id_usuario', 'token_usuario_idx');

            // Clave foránea
            $table->foreign('id_usuario', 'token_usuario_fk')
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
        Schema::dropIfExists('tokens');
    }
};
