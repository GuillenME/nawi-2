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
        Schema::create('fotos', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->longText('url');
            $table->dateTime('fecha_subida');
            $table->char('id_usuario', 36);
            $table->timestamps();

            // Clave foránea
            $table->foreign('id_usuario', 'foto_usuario_fk')
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
        Schema::dropIfExists('fotos');
    }
};
