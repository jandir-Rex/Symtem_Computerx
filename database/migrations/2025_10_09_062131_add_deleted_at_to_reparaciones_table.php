<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            // Agregamos la columna deleted_at para el SoftDeletes
            if (!Schema::hasColumn('reparaciones', 'deleted_at')) {
                $table->softDeletes()->comment('Fecha de eliminación lógica');
            }
        });
    }

    public function down(): void
    {
        Schema::table('reparaciones', function (Blueprint $table) {
            // Eliminamos la columna si se revierte la migración
            if (Schema::hasColumn('reparaciones', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });
    }
};
