<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // <-- Agrega esta línea

return new class extends Migration {
    public function up()
    {
        Schema::create('stands', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->comment('Nombre del stand');
            $table->string('ubicacion')->nullable()->comment('Ubicación física');
            $table->enum('tipo', ['productos', 'reparacion'])->comment('Tipo de stand');
            $table->boolean('activo')->default(true)->comment('Stand habilitado');
            $table->text('descripcion')->nullable()->comment('Descripción del stand');
            $table->timestamps();
        });

        // Insertar los 2 stands por defecto
        DB::table('stands')->insert([
            [
                'id' => 1,
                'nombre' => 'Stand 1 - Productos',
                'tipo' => 'productos',
                'ubicacion' => 'Local principal',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'id' => 2,
                'nombre' => 'Stand 2 - Reparación y Mantenimiento',
                'tipo' => 'reparacion',
                'ubicacion' => 'Local de servicios',
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('stands');
    }
    
};
