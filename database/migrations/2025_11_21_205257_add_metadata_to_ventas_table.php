<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Campo JSON para almacenar datos adicionales del pedido
            $table->json('metadata')->nullable()->after('observaciones');
        });

        // Cambiar observaciones de VARCHAR a TEXT (sin perder datos)
        DB::statement('ALTER TABLE ventas MODIFY observaciones TEXT');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn('metadata');
        });
        
        // Revertir observaciones a VARCHAR(500)
        DB::statement('ALTER TABLE ventas MODIFY observaciones VARCHAR(500)');
    }
};