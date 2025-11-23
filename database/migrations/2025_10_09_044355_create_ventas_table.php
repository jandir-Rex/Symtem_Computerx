<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();

            // Relaciones
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('stand_id')->constrained('stands')->onDelete('cascade');

            // Datos principales
            $table->string('tipo_comprobante')->nullable(); // boleta / factura
            $table->string('numero_comprobante')->nullable();
            $table->enum('tipo_pago', ['contado', 'credito'])->default('contado');
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'tarjeta'])->default('efectivo');

            // Estado del pago
            $table->boolean('pagado')->default(false);
            $table->dateTime('fecha_vencimiento')->nullable();
            $table->dateTime('fecha_pago')->nullable();

            // ✅ TOTALES CON IGV DESGLOSADO
            $table->decimal('subtotal', 10, 2)->default(0)->comment('Base imponible sin IGV');
            $table->decimal('igv', 10, 2)->default(0)->comment('Monto del IGV (18%)');
            $table->decimal('total', 10, 2)->default(0)->comment('Total con IGV incluido');
            
            // Observaciones y datos adicionales
            $table->text('observaciones')->nullable();
            $table->string('celular_cliente', 20)->nullable();
            $table->decimal('monto_cuota', 10, 2)->nullable();
            $table->json('fechas_pago')->nullable();

            // Datos SUNAT
            $table->string('estado_sunat')->nullable();
            $table->string('hash_sunat')->nullable();
            $table->text('mensaje_sunat')->nullable();

            // Control interno
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};