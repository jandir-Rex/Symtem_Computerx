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
        Schema::create('egresos', function (Blueprint $table) {
            $table->id();
            $table->string('documento_tipo'); // Tipo de comprobante (Factura, etc.)
            $table->string('descripcion');
            $table->decimal('total', 10, 2); // Monto Total con IGV
            $table->date('fecha_emision');
            $table->unsignedBigInteger('proveedor_id')->nullable(); 
            $table->timestamps();

            // Opcional: Clave foránea si manejas proveedores
            // $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egresos');
    }
};