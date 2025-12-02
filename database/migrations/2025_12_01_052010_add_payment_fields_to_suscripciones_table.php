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
        Schema::table('suscripciones', function (Blueprint $table) {
            $table->string('metodo_pago', 50)->nullable()->after('precio');
            $table->string('referencia_pago', 255)->nullable()->after('metodo_pago');
            $table->dateTime('fecha_pago')->nullable()->after('referencia_pago');
            $table->string('estado_pago', 20)->default('pendiente')->after('fecha_pago');
            $table->string('id_transaccion', 255)->nullable()->after('estado_pago');
            $table->decimal('monto', 10, 2)->nullable()->after('id_transaccion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('suscripciones', function (Blueprint $table) {
            $table->dropColumn([
                'metodo_pago',
                'referencia_pago',
                'fecha_pago',
                'estado_pago',
                'id_transaccion',
                'monto'
            ]);
        });
    }
};
