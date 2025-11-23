<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reparaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stand_id')->constrained('stands')->comment('Stand que realiza el servicio');
            
            // Datos del cliente (sin FK porque no tienes tabla clientes)
            $table->string('cliente_nombre', 100)->comment('Nombre del cliente');
            $table->string('cliente_telefono', 20)->comment('Teléfono del cliente');
            $table->string('cliente_email', 100)->nullable()->comment('Email del cliente');
            
            // Datos del equipo
            $table->string('tipo_equipo', 50)->comment('Tipo de equipo (Laptop, PC, etc)');
            $table->string('marca', 50)->nullable()->comment('Marca del equipo');
            $table->string('modelo', 50)->nullable()->comment('Modelo del equipo');
            $table->string('numero_serie', 100)->nullable()->comment('Número de serie');
            
            // Problema y diagnóstico
            $table->text('problema_reportado')->comment('Problema reportado por el cliente');
            $table->text('diagnostico')->nullable()->comment('Diagnóstico técnico');
            $table->text('solucion_aplicada')->nullable()->comment('Solución aplicada');
            
            // Estado y control
            $table->enum('estado', [
                'recibido', 
                'diagnosticando', 
                'en_reparacion', 
                'esperando_repuestos', 
                'listo', 
                'entregado', 
                'cancelado'
            ])->default('recibido')->comment('Estado del servicio');
            
            $table->enum('prioridad', ['baja', 'normal', 'alta', 'urgente'])
                  ->default('normal')
                  ->comment('Prioridad del servicio');
            
            // Costos
            $table->decimal('costo_diagnostico', 10, 2)->default(0)->comment('Costo del diagnóstico');
            $table->decimal('costo_mano_obra', 10, 2)->default(0)->comment('Costo de mano de obra');
            $table->decimal('costo_repuestos', 10, 2)->default(0)->comment('Costo de repuestos');
            $table->decimal('costo_total', 10, 2)->default(0)->comment('Costo total');
            
            // Asignación
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->comment('Usuario que registró');
            $table->string('tecnico_asignado', 100)->nullable()->comment('Técnico asignado');
            
            // Fechas
            $table->timestamp('fecha_ingreso')->useCurrent()->comment('Fecha de ingreso');
            $table->timestamp('fecha_estimada_entrega')->nullable()->comment('Fecha estimada de entrega');
            $table->timestamp('fecha_entrega_real')->nullable()->comment('Fecha real de entrega');
            
            // Extras
            $table->text('notas_internas')->nullable()->comment('Notas internas');
            $table->text('accesorios_incluidos')->nullable()->comment('Accesorios que vienen con el equipo');
            $table->tinyInteger('calificacion')->nullable()->comment('Calificación del cliente (1-5)');
            
            // Auditoría
            $table->timestamps();
            $table->softDeletes();
            
            // Índices
            $table->index('stand_id');
            $table->index('estado');
            $table->index('cliente_telefono');
            $table->index('fecha_ingreso');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reparaciones');
    }
};