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
        Schema::table('taxistas', function (Blueprint $table) {
            // Permitir NULL en documentos
            $table->char('id_matricula', 36)->nullable()->change();
            $table->char('id_licencia', 36)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('taxistas', function (Blueprint $table) {
            // Revertir a NOT NULL
            $table->char('id_matricula', 36)->nullable(false)->change();
            $table->char('id_licencia', 36)->nullable(false)->change();
        });
    }
};
