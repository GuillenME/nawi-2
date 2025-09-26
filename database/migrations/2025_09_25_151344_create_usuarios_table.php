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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('nombre', 45);
            $table->string('apellido', 45);
            $table->string('telefono', 15);
            $table->string('email', 100);
            $table->string('password', 60);
            $table->char('id_rol', 36);
            $table->timestamps();

            // Índices
            $table->index('id_rol', 'usuario_rol_idx');

            // Clave foránea
            $table->foreign('id_rol', 'usuario_rol_fk')
                  ->references('id')
                  ->on('roles')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
