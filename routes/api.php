<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Producto;

Route::middleware('auth:sanctum')->get('/productos/codigo/{codigo}', function($codigo){
    $producto = Producto::where('codigo_barras', $codigo)
                ->where('activo', true)
                ->first();

    if (!$producto) {
        return response()->json(['success' => false, 'message' => 'Producto no encontrado'], 404);
    }

    return response()->json([
        'success' => true,
        'producto' => [
            'id' => $producto->id,
            'nombre' => $producto->nombre,
            'stock' => $producto->stock,
            'precio_sugerido' => (float) ($producto->precio_venta ?? 0),
        ]
    ]);
});
