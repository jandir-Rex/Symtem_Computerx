<?php

// database/seeders/ProductosSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;

class ProductosSeeder extends Seeder
{
    public function run()
    {
        $productos = [
            ['codigo' => 'PROD001', 'codigo_barras' => '7501234567890', 'nombre' => 'Laptop HP Pavilion', 'precio_compra' => 2000, 'precio_venta' => 2500, 'stock' => 10],
            ['codigo' => 'PROD002', 'codigo_barras' => '7501234567891', 'nombre' => 'Mouse Logitech', 'precio_compra' => 30, 'precio_venta' => 50, 'stock' => 50],
            ['codigo' => 'PROD003', 'codigo_barras' => '7501234567892', 'nombre' => 'Teclado Mecánico', 'precio_compra' => 80, 'precio_venta' => 120, 'stock' => 25],
            ['codigo' => 'PROD004', 'codigo_barras' => '7501234567893', 'nombre' => 'Monitor 24"', 'precio_compra' => 400, 'precio_venta' => 600, 'stock' => 15],
            ['codigo' => 'REP001', 'codigo_barras' => '7501234567894', 'nombre' => 'Memoria RAM 8GB', 'precio_compra' => 100, 'precio_venta' => 150, 'stock' => 30],
        ];

        foreach ($productos as $producto) {
            Producto::create($producto);
        }
    }
}