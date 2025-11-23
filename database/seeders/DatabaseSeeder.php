<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder; // <-- 1. ¡IMPORTA LA CLASE CORRECTA!

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 2. ¡Llama a la clase con el nombre correcto!
        $this->call([
            RoleSeeder::class, // <-- CORREGIDO: De 'RolesAndUsersSeeder' a 'RoleSeeder'
            // Si tienes otros seeders, añádelos aquí:
            // StandSeeder::class,
            // ProductoSeeder::class,
        ]);
    }
}