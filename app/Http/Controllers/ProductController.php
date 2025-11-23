<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * 🏠 HOME - Mostrar productos visibles en e-commerce
     */
    public function home()
    {
        // Productos destacados para el slider
        $destacados = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('destacado', true)
            ->where('stock', '>', 0)
            ->limit(6)
            ->get();

        // Si no hay destacados, traer los más recientes visibles
        if ($destacados->isEmpty()) {
            $destacados = Producto::where('visible_ecommerce', true)
                ->where('activo', true)
                ->where('stock', '>', 0)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }

        // Productos por categoría
        $laptops = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'laptops')
            ->limit(8)
            ->get();

        $pcsGamer = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'pc')
            ->limit(8)
            ->get();

        $componentes = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'componentes')
            ->limit(8)
            ->get();

        $perifericos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'perifericos')
            ->limit(8)
            ->get();

        $monitores = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'monitores')
            ->limit(8)
            ->get();

        $accesorios = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'accesorios')
            ->limit(8)
            ->get();

        // Productos recién agregados
        $nuevos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        return view('home', compact(
            'destacados',
            'laptops',
            'pcsGamer',
            'componentes',
            'perifericos',
            'monitores',
            'accesorios',
            'nuevos'
        ));
    }

    /**
     * 💻 Laptops
     */
    public function laptops()
    {
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'laptops')
            ->paginate(16);

        return view('products.category', [
            'productos' => $productos,
            'titulo' => 'Laptops',
            'categoria' => 'laptops'
        ]);
    }

    /**
     * 🖥️ PC Gaming
     */
    public function pcGaming()
    {
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'pc')
            ->paginate(16);

        return view('products.category', [
            'productos' => $productos,
            'titulo' => 'PC Gaming',
            'categoria' => 'pc-gaming'
        ]);
    }

    /**
     * 🔧 Componentes
     */
    public function componentes()
    {
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'componentes')
            ->paginate(16);

        return view('products.category', [
            'productos' => $productos,
            'titulo' => 'Componentes',
            'categoria' => 'componentes'
        ]);
    }

    /**
     * ⌨️ Periféricos
     */
    public function perifericos()
    {
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'perifericos')
            ->paginate(16);

        return view('products.category', [
            'productos' => $productos,
            'titulo' => 'Periféricos',
            'categoria' => 'perifericos'
        ]);
    }

    /**
     * 🖥️ Monitores
     */
    public function monitores()
    {
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'monitores')
            ->paginate(16);

        return view('products.category', [
            'productos' => $productos,
            'titulo' => 'Monitores',
            'categoria' => 'monitores'
        ]);
    }

    /**
     * 🎮 Consolas
     */
    public function consolas()
    {
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'consolas')
            ->paginate(16);

        return view('products.category', [
            'productos' => $productos,
            'titulo' => 'Consolas',
            'categoria' => 'consolas'
        ]);
    }

    /**
     * 🎧 Accesorios
     */
    public function accesorios()
    {
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'accesorios')
            ->paginate(16);

        return view('products.category', [
            'productos' => $productos,
            'titulo' => 'Accesorios',
            'categoria' => 'accesorios'
        ]);
    }

    /**
     * 🔩 Repuestos
     */
    public function repuestos()
    {
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', 'repuestos')
            ->paginate(16);

        return view('products.category', [
            'productos' => $productos,
            'titulo' => 'Repuestos',
            'categoria' => 'repuestos'
        ]);
    }

    /**
     * 🔍 Búsqueda en vivo (AJAX)
     */
    public function searchLive(Request $request)
    {
        $term = $request->get('q', '');

        if (strlen($term) < 2) {
            return response()->json([]);
        }

        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where(function($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('marca', 'like', "%{$term}%")
                  ->orWhere('codigo_barras', 'like', "%{$term}%");
            })
            ->limit(10)
            ->get()
            ->map(function($p) {
                return [
                    'id' => $p->id,
                    'nombre' => $p->nombre,
                    'precio' => number_format($p->precio_venta, 2),
                    'imagen' => $p->imagen_url,
                    'url' => route('product.show', [$p->categoria_url, $p->id]),
                    'stock' => $p->stock
                ];
            });

        return response()->json($productos);
    }

    /**
     * 🔍 Búsqueda global
     */
    public function searchGlobal(Request $request)
    {
        $query = $request->get('q', '');

        // Si no hay término de búsqueda
        if (empty($query)) {
            return view('search', [
                'query' => '',
                'results' => []
            ]);
        }

        // Buscar productos
        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where(function($q) use ($query) {
                $q->where('nombre', 'like', "%{$query}%")
                  ->orWhere('marca', 'like', "%{$query}%")
                  ->orWhere('descripcion', 'like', "%{$query}%")
                  ->orWhere('codigo_barras', 'like', "%{$query}%");
            })
            ->get();

        // Mapear categorías a sus nombres legibles
        $categoriasLabels = [
            'laptops' => 'Laptops',
            'pc' => 'PC Gaming',
            'componentes' => 'Componentes',
            'perifericos' => 'Periféricos',
            'monitores' => 'Monitores',
            'consolas' => 'Consolas',
            'accesorios' => 'Accesorios',
            'repuestos' => 'Repuestos'
        ];

        // Formatear resultados para la vista
        $results = $productos->map(function($producto) use ($categoriasLabels) {
            return [
                'name' => $producto->nombre,
                'price' => $producto->precio_venta,
                'image' => $producto->imagen,
                'image_url' => $producto->imagen_url ?? asset('storage/' . $producto->imagen),
                'category' => $producto->categoria,
                'category_label' => $categoriasLabels[$producto->categoria] ?? ucfirst($producto->categoria)
            ];
        })->toArray();

        return view('search', [
            'query' => $query,
            'results' => $results
        ]);
    }

    /**
     * 📦 Mostrar detalle de producto
     * 🔥 MEJORADO: Acepta ID o slug
     */
    public function show($category, $idOrSlug)
    {
        // Mapear la categoría URL a la categoría de BD
        $categoriaDb = Producto::mapearCategoriaUrl($category);
        
        if (!$categoriaDb) {
            abort(404, 'Categoría no válida');
        }

        $producto = null;

        // 🔥 OPCIÓN 1: Si es numérico, buscar por ID (más rápido)
        if (is_numeric($idOrSlug)) {
            $producto = Producto::where('visible_ecommerce', true)
                ->where('activo', true)
                ->where('stock', '>', 0)
                ->where('categoria', $categoriaDb)
                ->where('id', $idOrSlug)
                ->first();
        }

        // 🔥 OPCIÓN 2: Si no es numérico o no se encontró, buscar por slug
        if (!$producto) {
            // Obtener todos los productos de la categoría y filtrar por slug en PHP
            $producto = Producto::where('visible_ecommerce', true)
                ->where('activo', true)
                ->where('stock', '>', 0)
                ->where('categoria', $categoriaDb)
                ->get()
                ->first(function ($item) use ($idOrSlug) {
                    return \Illuminate\Support\Str::slug($item->nombre) === $idOrSlug;
                });
        }

        // Si aún no se encuentra, 404
        if (!$producto) {
            abort(404, 'Producto no encontrado');
        }

        // Productos relacionados de la misma categoría
        $relacionados = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->where('categoria', $producto->categoria)
            ->where('id', '!=', $producto->id)
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return view('products.show', compact('producto', 'relacionados'));
    }

    /**
     * 🏷️ Filtrar por marca
     */
    public function byBrand($category, $brand)
    {
        $categoriaDb = Producto::mapearCategoriaUrl($category);
        $marcaNombre = str_replace('-', ' ', $brand);

        $productos = Producto::where('visible_ecommerce', true)
            ->where('activo', true)
            ->where('stock', '>', 0)
            ->when($categoriaDb, fn($q) => $q->where('categoria', $categoriaDb))
            ->where('marca', 'like', "%{$marcaNombre}%")
            ->paginate(16);

        $titulo = 'Productos ' . ucwords($marcaNombre);

        return view('products.category', compact('productos', 'titulo'));
    }
}