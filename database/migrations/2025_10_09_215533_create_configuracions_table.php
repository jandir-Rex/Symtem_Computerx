<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('configuracion', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa')->default('VentasSystem');
            $table->string('ruc', 11)->nullable();
            $table->string('direccion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('email')->nullable();
            $table->decimal('igv', 5, 2)->default(18.00);
            $table->integer('stock_minimo')->default(10);
            $table->integer('dias_credito')->default(30);
            $table->boolean('permitir_venta_sin_stock')->default(false);
            $table->boolean('imprimir_automatico')->default(true);
            $table->string('stand1_nombre')->default('Productos');
            $table->string('stand2_nombre')->default('Reparación');
            $table->boolean('stand1_activo')->default(true);
            $table->boolean('stand2_activo')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('configuracion');
    }
};