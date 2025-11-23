<?php

// database/seeders/ClientesSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cliente;

class ClientesSeeder extends Seeder
{
    public function run()
    {
        $clientes = [
            ['nombre' => 'Juan Pérez', 'documento' => '12345678', 'telefono' => '987654321', 'email' => 'juan@email.com'],
            ['nombre' => 'María García', 'documento' => '87654321', 'telefono' => '987654322', 'email' => 'maria@email.com'],
            ['nombre' => 'Carlos López', 'documento' => '11223344', 'telefono' => '987654323', 'email' => 'carlos@email.com'],
        ];

        foreach ($clientes as $cliente) {
            Cliente::create($cliente);
        }
    }
}