<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('documento', 20)->nullable()->unique();
            $table->string('celular', 20)->nullable();
            $table->string('email')->nullable()->unique();
            $table->text('direccion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index('documento');
            $table->index('activo');
        });
    }

    public function down()
    {
        Schema::dropIfExists('clientes');
    }
};