<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->string('estado_pedido')->default('pendiente')->after('pagado');
            // pendiente = recién creado y pagado, listo para enviar
            // atendido = ya fue procesado/enviado por el admin
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('estado_pedido');
        });
    }
};