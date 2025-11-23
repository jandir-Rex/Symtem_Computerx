<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Proveedor; // Asegúrate de que el namespace sea correcto
use Illuminate\Support\Facades\DB;

class ProveedorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar la tabla antes de sembrar, opcional pero recomendado
        DB::table('proveedors')->delete();

        $proveedores = [
            [
                'ruc' => '20100100101', 
                'nombre' => 'MOVISTAR PERÚ S.A.', 
                'telefono' => '0800-11111', 
                'email' => 'ventas@movistar.pe',
                'created_at' => now(), 
                'updated_at' => now(),
            ],
            [
                'ruc' => '20100100102', 
                'nombre' => 'ENEL DISTRIBUCIÓN S.A.', 
                'telefono' => '0800-22222', 
                'email' => 'contacto@enel.pe',
                'created_at' => now(), 
                'updated_at' => now(),
            ],
            [
                'ruc' => '20510100103', 
                'nombre' => 'CENCOSUD RETAIL PERÚ S.A. (Metro)', 
                'telefono' => '0800-33333', 
                'email' => 'atencion@cencosud.pe',
                'created_at' => now(), 
                'updated_at' => now(),
            ],
            [
                'ruc' => '10450100104', 
                'nombre' => 'Importadora Tecnológica Global SAC', 
                'telefono' => '999-888-777', 
                'email' => 'contacto@tecnoperu.com',
                'created_at' => now(), 
                'updated_at' => now(),
            ],
        ];

        DB::table('proveedors')->insert($proveedores);

        $this->command->info('Proveedores de prueba cargados exitosamente.');
    }
}