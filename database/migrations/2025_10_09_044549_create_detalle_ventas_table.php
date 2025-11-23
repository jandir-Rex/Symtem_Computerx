<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id();

            // Relación con la venta
            $table->foreignId('venta_id')
                  ->constrained('ventas')
                  ->onDelete('cascade'); // Si se elimina una venta, se eliminan sus detalles

            // Relación con el producto
            $table->foreignId('producto_id')
                  ->constrained('productos')
                  ->onDelete('restrict'); // No se puede eliminar un producto con ventas registradas

            // Campos editables desde la interfaz
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('detalle_ventas');
    }
};
