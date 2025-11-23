<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            // Datos básicos
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            // Campos adicionales según tu modelo
            $table->string('dni')->nullable();
            $table->string('role')->nullable(); // Puedes eliminarlo si solo usarás spatie roles
            $table->unsignedBigInteger('stand_id')->nullable();
            $table->boolean('active')->default(true);
            $table->integer('failed_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();

            // Soporte para login
            $table->rememberToken();
            $table->timestamp('email_verified_at')->nullable();

            $table->timestamps();

            // Relaciones
            $table->foreign('stand_id')
                  ->references('id')
                  ->on('stands')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};
