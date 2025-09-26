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
        Schema::create('taxis', function (Blueprint $table) {
            $table->char('id', 36)->primary();
            $table->string('marca', 45);
            $table->string('modelo', 45);
            $table->char('id_taxista', 36);
            $table->timestamps();

            // Índices
            $table->index('id_taxista', 'taxi_taxista_idx');

            // Clave foránea
            $table->foreign('id_taxista', 'taxi_taxista_fk')
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
        Schema::dropIfExists('taxis');
    }
};
