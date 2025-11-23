<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();

            // Código de barras (único si existe)
            $table->string('codigo_barras')->nullable()->unique();

            // Datos generales
            $table->string('categoria')->nullable();
            $table->string('marca')->nullable();
            $table->unsignedInteger('garantia_meses')->default(0);

            // Control de inventario y precios
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('precio_compra', 12, 2)->default(0);
            $table->decimal('precio_venta', 12, 2)->default(0);

            // Control de visibilidad
            $table->boolean('activo')->default(true);
            $table->boolean('visible_ecommerce')->default(false);
            $table->boolean('destacado')->default(false);

            // Imagen del producto
            $table->string('imagen')->nullable();

            // Usuario que creó o editó (almacenista)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete(); // opcional, elimina productos si se borra el usuario

            $table->timestamps();
            $table->softDeletes();

            // Índices
            $table->index(['categoria', 'marca']);
            $table->index(['activo', 'visible_ecommerce']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
