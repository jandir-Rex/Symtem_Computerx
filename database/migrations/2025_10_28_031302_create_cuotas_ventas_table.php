<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración.
     */
    public function up(): void
    {
        Schema::create('cuotas_ventas', function (Blueprint $table) {
            $table->id();

            // Relación con la tabla ventas
            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->onDelete('cascade');

            // Número de cuota (1, 2, 3, ...)
            $table->integer('numero_cuota');

            // Monto que corresponde a esta cuota
            $table->decimal('monto', 10, 2);

            // Fecha límite para pagar la cuota
            $table->date('fecha_vencimiento')->nullable();

            // Si la cuota ya fue pagada o no
            $table->boolean('pagada')->default(false);

            // Fecha exacta en que se pagó (si aplica)
            $table->date('fecha_pago')->nullable();

            // Campos automáticos de Laravel
            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas_ventas');
    }
};
