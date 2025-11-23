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
        Schema::table('ventas', function (Blueprint $table) {
            // Se requiere doctrine/dbal para usar change() en ENUM/VARCHAR
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta', 'tarjeta_stripe'])->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Revertir a los valores originales si es necesario
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta'])->change();
        });
    }
};