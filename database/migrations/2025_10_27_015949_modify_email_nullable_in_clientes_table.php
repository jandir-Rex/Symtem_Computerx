<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Hacer el email nullable solo si existe la columna
            if (Schema::hasColumn('clientes', 'email')) {
                try {
                    // Quitar unique si existía
                    DB::statement("ALTER TABLE clientes DROP INDEX clientes_email_unique");
                } catch (\Throwable $th) {
                    // Silencia el error si no existe el índice
                }

                $table->string('email')->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (Schema::hasColumn('clientes', 'email')) {
                // Revertir el cambio: volver a NOT NULL y unique si quieres
                $table->string('email')->unique()->nullable(false)->change();
            }
        });
    }
};
