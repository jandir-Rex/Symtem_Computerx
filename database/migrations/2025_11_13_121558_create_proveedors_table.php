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
        // NOTA: Laravel por defecto usa el plural para el nombre de la tabla
        // que corresponde al modelo singular (Proveedor -> proveedors)
        Schema::create('proveedors', function (Blueprint $table) {
            $table->id();
            $table->string('ruc', 11)->unique()->comment('Número RUC para identificación SUNAT');
            $table->string('nombre', 150)->comment('Razón Social o Nombre del Proveedor');
            $table->string('telefono', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedors');
    }
};