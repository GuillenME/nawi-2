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
        Schema::table('viajes', function (Blueprint $table) {
            // Tiempo límite para aceptar el viaje (por defecto 5 minutos desde la creación)
            if (!Schema::hasColumn('viajes', 'tiempo_limite_aceptacion')) {
                $table->timestamp('tiempo_limite_aceptacion')->nullable()->after('fecha_completado');
            }
            // Tarifa que el taxista establece al aceptar el viaje
            if (!Schema::hasColumn('viajes', 'tarifa')) {
                $table->decimal('tarifa', 10, 2)->nullable()->after('tiempo_limite_aceptacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('viajes', function (Blueprint $table) {
            if (Schema::hasColumn('viajes', 'tiempo_limite_aceptacion')) {
                $table->dropColumn('tiempo_limite_aceptacion');
            }
            if (Schema::hasColumn('viajes', 'tarifa')) {
                $table->dropColumn('tarifa');
            }
        });
    }
};
