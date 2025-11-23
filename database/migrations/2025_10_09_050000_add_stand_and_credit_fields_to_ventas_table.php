<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Agregar columnas si no existen
            if (!Schema::hasColumn('ventas', 'pagado')) {
                $table->boolean('pagado')->default(true)->after('tipo_pago')->comment('Si el crédito está pagado');
                // Crear índice solo si no existe
                DB::statement("CREATE INDEX IF NOT EXISTS ventas_pagado_index ON ventas(pagado)");
            }

            if (!Schema::hasColumn('ventas', 'fecha_vencimiento')) {
                $table->date('fecha_vencimiento')->nullable()->after('pagado')->comment('Fecha límite de pago');
            }

            if (!Schema::hasColumn('ventas', 'fecha_pago')) {
                $table->date('fecha_pago')->nullable()->after('fecha_vencimiento')->comment('Fecha real de pago');
            }

            if (!Schema::hasColumn('ventas', 'observaciones')) {
                $table->text('observaciones')->nullable()->after('fecha_pago')->comment('Notas adicionales');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // 🔧 Intentar eliminar el índice de forma segura
            try {
                DB::statement("DROP INDEX ventas_pagado_index ON ventas");
            } catch (\Throwable $th) {
                // Silenciar error si el índice no existe
            }

            // 🔧 Eliminar columnas si existen
            $cols = ['pagado', 'fecha_vencimiento', 'fecha_pago', 'observaciones'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('ventas', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
