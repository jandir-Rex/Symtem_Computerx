<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\CuotaVenta;
use App\Models\Egreso;
use App\Models\Reparacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if (!$user->roles || $user->roles->isEmpty()) {
            auth()->logout();
            return redirect()->route('login')->with('error', '❌ Tu cuenta no tiene un rol asignado. Contacta al administrador.');
        }

        if ($user->hasRole('Administrador')) {
            return $this->dashboardAdministrador();
        }

        if ($user->hasRole('Stand1')) {
            return redirect()->route('stands.stand1.dashboard');
        }

        if ($user->hasRole('Stand2')) {
            return redirect()->route('stands.stand2.dashboard');
        }

        if ($user->hasRole('Contador')) {
            return $this->dashboardContador();
        }

        if ($user->hasRole('Almacen')) {
            return redirect()->route('dashboard.almacen.index');
        }

        $pedidosPendientesCount = Venta::where(function($q) {
                                           $q->where('observaciones', 'like', '%Stripe%')
                                             ->orWhere('observaciones', 'like', '%Session ID%')
                                             ->orWhere('observaciones', 'like', '%Método de pago:%');
                                       })
                                       ->where(function($q) {
                                           $q->whereNull('estado_pedido')
                                             ->orWhere('estado_pedido', '')
                                             ->orWhereIn('estado_pedido', ['pendiente', 'en_preparacion', 'PENDIENTE', 'EN_PREPARACION']);
                                       })
                                       ->count();

        return view('dashboard.index', compact('pedidosPendientesCount'));

        auth()->logout();
        return redirect()->route('login')->with('error', '❌ Tu cuenta no tiene permisos válidos.');
    }

    private function dashboardAdministrador()
    {
        $pedidosPendientesCount = Venta::where(function($q) {
                                           $q->where('observaciones', 'like', '%Stripe%')
                                             ->orWhere('observaciones', 'like', '%Session ID%')
                                             ->orWhere('observaciones', 'like', '%Método de pago:%');
                                       })
                                       ->where(function($q) {
                                           $q->whereNull('estado_pedido')
                                             ->orWhere('estado_pedido', '')
                                             ->orWhereIn('estado_pedido', ['pendiente', 'en_preparacion', 'PENDIENTE', 'EN_PREPARACION']);
                                       })
                                       ->count();

        $stats = [
            'ventas_mes' => $this->getVentasMes(),
            'total_clientes' => Cliente::count(),
            'nuevos_clientes' => Cliente::whereMonth('created_at', Carbon::now()->month)->count(),
            'productos_activos' => Producto::where('activo', true)->count(),
            'stock_bajo' => Producto::whereRaw('stock <= stock_minimo')->count(),
            'cuotas_pendientes' => CuotaVenta::where('pagada', false)->sum('monto'),
            'cuotas_vencidas' => CuotaVenta::where('pagada', false)
                                      ->where('fecha_vencimiento', '<', Carbon::now())
                                      ->count(),
            'total_skus' => Producto::count(),
            'total_unidades' => Producto::sum('stock'),
            'alerta_stock_bajo' => Producto::whereRaw('stock <= stock_minimo')->count(),
            'visible_ecommerce' => Producto::where('visible_ecommerce', true)->count(),
            'pedidos_nuevos' => $pedidosPendientesCount,
        ];

        $ventasMensuales = $this->getVentasMensuales();
        $topProductos = $this->getTopProductos();
        $productosStockBajo = Producto::whereRaw('stock <= stock_minimo')
                                      ->orderBy('stock', 'asc')
                                      ->limit(10)
                                      ->get();

        $cuotasChart = [
            CuotaVenta::where('pagada', true)->count(),
            CuotaVenta::where('pagada', false)->where('fecha_vencimiento', '>=', Carbon::now())->count(),
            CuotaVenta::where('pagada', false)->where('fecha_vencimiento', '<', Carbon::now())->count()
        ];

        $ventasStand1 = Venta::where('stand_id', 1)->whereMonth('created_at', Carbon::now()->month)->sum('total');
        $ventasStand2 = Venta::where('stand_id', 2)->whereMonth('created_at', Carbon::now()->month)->sum('total');

        $categorias = Producto::getCategorias();

        return view('dashboard.index', compact(
            'stats',
            'ventasMensuales',
            'topProductos',
            'productosStockBajo',
            'cuotasChart',
            'ventasStand1',
            'ventasStand2',
            'categorias',
            'pedidosPendientesCount'
        ));
    }

    private function dashboardContador()
    {
        $pedidosPendientesCount = Venta::where(function($q) {
                                           $q->where('observaciones', 'like', '%Stripe%')
                                             ->orWhere('observaciones', 'like', '%Session ID%')
                                             ->orWhere('observaciones', 'like', '%Método de pago:%');
                                       })
                                       ->where(function($q) {
                                           $q->whereNull('estado_pedido')
                                             ->orWhere('estado_pedido', '')
                                             ->orWhereIn('estado_pedido', ['pendiente', 'en_preparacion', 'PENDIENTE', 'EN_PREPARACION']);
                                       })
                                       ->count();

        $mesActual = Carbon::now()->month;
        $anioActual = Carbon::now()->year;

        $meses = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
        $mesReporte = $meses[$mesActual - 1] . ' ' . $anioActual;

        $ingresosTotal = Venta::whereMonth('created_at', $mesActual)
                              ->whereYear('created_at', $anioActual)
                              ->sum('total');

        $egresosTotal = Egreso::whereMonth('fecha_emision', $mesActual)
                              ->whereYear('fecha_emision', $anioActual)
                              ->sum('total');

        $igvVentas = $ingresosTotal * 0.18 / 1.18;
        $igvEgresos = $egresosTotal * 0.18 / 1.18;
        $igvACargo = $igvVentas - $igvEgresos;

        $utilidadBruta = ($ingresosTotal / 1.18) - ($egresosTotal / 1.18);

        $ventasAnuales = Venta::whereYear('created_at', $anioActual)->sum('total');
        $rentaAnualPagar = $ventasAnuales * 0.30;

        $labelsAnuales = [];
        $dataIngresos = [];
        $dataEgresos = [];
        $dataUtilidad = [];

        for ($mes = 1; $mes <= 12; $mes++) {
            $labelsAnuales[] = $meses[$mes - 1];
            
            $ingresoMes = Venta::whereMonth('created_at', $mes)
                               ->whereYear('created_at', $anioActual)
                               ->sum('total');
            
            $egresoMes = Egreso::whereMonth('fecha_emision', $mes)
                               ->whereYear('fecha_emision', $anioActual)
                               ->sum('total');
            
            $dataIngresos[] = round($ingresoMes / 1.18, 2);
            $dataEgresos[] = round($egresoMes / 1.18, 2);
            $dataUtilidad[] = round(($ingresoMes - $egresoMes) / 1.18, 2);
        }

        $stats = [
            'ingresos_mes' => $ingresosTotal,
            'egresos_mes' => $egresosTotal,
            'utilidad_mes' => $ingresosTotal - $egresosTotal,
            'cuotas_pendientes' => CuotaVenta::where('pagada', false)->sum('monto'),
            'cuotas_vencidas' => CuotaVenta::where('pagada', false)
                                          ->where('fecha_vencimiento', '<', Carbon::now())
                                          ->sum('monto'),
            'total_clientes' => Cliente::count(),
        ];

        $ingresosChart = [];
        $egresosChart = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $ingresosChart[] = Venta::whereMonth('created_at', $mes)
                                    ->whereYear('created_at', $anioActual)
                                    ->sum('total');
            $egresosChart[] = Egreso::whereMonth('fecha_emision', $mes)
                                    ->whereYear('fecha_emision', $anioActual)
                                    ->sum('total');
        }

        $ventasStand1 = Venta::where('stand_id', 1)->whereMonth('created_at', $mesActual)->sum('total');
        $ventasStand2 = Venta::where('stand_id', 2)->whereMonth('created_at', $mesActual)->sum('total');

        $clientesDeuda = Cliente::whereHas('ventas', function($q) {
            $q->where('metodo_pago', 'credito')
              ->whereHas('cuotas', fn($sq) => $sq->where('pagada', false));
        })
        ->with(['ventas' => function($q) {
            $q->where('metodo_pago', 'credito')
              ->whereHas('cuotas', fn($sq) => $sq->where('pagada', false));
        }])
        ->limit(10)
        ->get()
        ->map(function($cliente) {
            $cliente->deuda = $cliente->ventas->sum(function($venta) {
                return $venta->cuotas->where('pagada', false)->sum('monto');
            });
            return $cliente;
        })
        ->sortByDesc('deuda');

        return view('contador.index', compact(
            'mesReporte',
            'ingresosTotal',
            'egresosTotal',
            'igvVentas',
            'igvEgresos',
            'igvACargo',
            'utilidadBruta',
            'rentaAnualPagar',
            'labelsAnuales',
            'dataIngresos',
            'dataEgresos',
            'dataUtilidad',
            'stats',
            'ingresosChart',
            'egresosChart',
            'ventasStand1',
            'ventasStand2',
            'clientesDeuda',
            'pedidosPendientesCount'
        ));
    }

    // ============================================
    // 🛒 VENTAS E-COMMERCE
    // ============================================
    
    public function ventasEcommerceIndex(Request $request)
    {
        $estado = $request->get('estado', 'todos');

        $query = Venta::with(['cliente', 'detalles.producto'])
                      ->where(function($q) {
                          $q->where('observaciones', 'like', '%Stripe%')
                            ->orWhere('observaciones', 'like', '%Session ID%')
                            ->orWhere('observaciones', 'like', '%Método de pago:%');
                      })
                      ->orderBy('created_at', 'desc');

        if ($estado === 'pendiente') {
            $query->where(function($q) {
                $q->whereNull('estado_pedido')
                  ->orWhere('estado_pedido', '')
                  ->orWhere(function($q2) {
                      $q2->whereNotNull('estado_pedido')
                         ->whereRaw("LOWER(estado_pedido) NOT IN ('atendido', 'entregado', 'completado', 'aceptado')");
                  });
            });
        } elseif ($estado === 'atendido') {
            $query->where(function($q) {
                $q->whereRaw("LOWER(estado_pedido) IN ('atendido', 'entregado', 'completado', 'aceptado')");
            });
        }

        if ($request->filled('buscar')) {
            $term = $request->buscar;
            $query->whereHas('cliente', function($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('documento', 'like', "%{$term}%");
            });
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $ventas = $query->paginate(20)->withQueryString();

        $baseQuery = Venta::where(function($q) {
            $q->where('observaciones', 'like', '%Stripe%')
              ->orWhere('observaciones', 'like', '%Session ID%')
              ->orWhere('observaciones', 'like', '%Método de pago:%');
        });
        
        $stats = [
            'total_ventas' => (clone $baseQuery)->sum('total'),
            'cantidad_ventas' => (clone $baseQuery)->count(),
            'pendientes' => (clone $baseQuery)->where(function($q) {
                $q->whereNull('estado_pedido')
                  ->orWhere('estado_pedido', '')
                  ->orWhereRaw("LOWER(estado_pedido) = 'pendiente'")
                  ->orWhereRaw("LOWER(estado_pedido) = 'en_preparacion'")
                  ->orWhereRaw("LOWER(estado_pedido) LIKE '%pendiente%'")
                  ->orWhereRaw("LOWER(estado_pedido) LIKE '%preparacion%'");
            })->count(),
            'atendidos' => (clone $baseQuery)->where(function($q) {
                $q->whereRaw("LOWER(estado_pedido) = 'atendido'")
                  ->orWhereRaw("LOWER(estado_pedido) = 'entregado'")
                  ->orWhereRaw("LOWER(estado_pedido) = 'completado'")
                  ->orWhereRaw("LOWER(estado_pedido) = 'aceptado'");
            })->count(),
        ];

        return view('dashboard.ventas-ecommerce.index', compact('ventas', 'stats', 'estado'));
    }

    public function ventasEcommerceMarcarAtendido($id)
    {
        try {
            $venta = Venta::where(function($q) {
                              $q->where('observaciones', 'like', '%Stripe%')
                                ->orWhere('observaciones', 'like', '%Session ID%')
                                ->orWhere('observaciones', 'like', '%Método de pago:%');
                          })
                          ->findOrFail($id);
            
            $venta->estado_pedido = 'atendido';
            $venta->save();

            return response()->json([
                'success' => true,
                'message' => 'Pedido marcado como atendido'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ventasEcommerceMarcarPendiente($id)
    {
        try {
            $venta = Venta::where(function($q) {
                              $q->where('observaciones', 'like', '%Stripe%')
                                ->orWhere('observaciones', 'like', '%Session ID%')
                                ->orWhere('observaciones', 'like', '%Método de pago:%');
                          })
                          ->findOrFail($id);
            
            $venta->estado_pedido = 'pendiente';
            $venta->save();

            return response()->json([
                'success' => true,
                'message' => 'Pedido marcado como pendiente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    public function ventasEcommerceShow($id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto'])
                      ->where(function($q) {
                          $q->where('observaciones', 'like', '%Stripe%')
                            ->orWhere('observaciones', 'like', '%Session ID%')
                            ->orWhere('observaciones', 'like', '%Método de pago:%');
                      })
                      ->findOrFail($id);
        
        return view('dashboard.ventas-ecommerce.show', compact('venta'));
    }

    public function ventasEcommerceDestroy($id)
    {
        try {
            $venta = Venta::where(function($q) {
                              $q->where('observaciones', 'like', '%Stripe%')
                                ->orWhere('observaciones', 'like', '%Session ID%')
                                ->orWhere('observaciones', 'like', '%Método de pago:%');
                          })
                          ->findOrFail($id);
            
            foreach ($venta->detalles as $detalle) {
                $producto = Producto::find($detalle->producto_id);
                if ($producto) {
                    $producto->increment('stock', $detalle->cantidad);
                }
            }
            
            $venta->detalles()->delete();
            $venta->delete();

            return redirect()->route('dashboard.ventas-ecommerce.index')
                             ->with('success', '✅ Venta eliminada y stock restaurado');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.ventas-ecommerce.index')
                             ->with('error', '❌ Error: ' . $e->getMessage());
        }
    }

    public function ventasEcommerceExportar(Request $request)
    {
        $query = Venta::with(['cliente', 'detalles.producto'])
                      ->where(function($q) {
                          $q->where('observaciones', 'like', '%Stripe%')
                            ->orWhere('observaciones', 'like', '%Session ID%')
                            ->orWhere('observaciones', 'like', '%Método de pago:%');
                      })
                      ->orderBy('created_at', 'desc');

        $estado = $request->get('estado');
        if ($estado === 'pendiente') {
            $query->where(function($q) {
                $q->whereNull('estado_pedido')
                  ->orWhere('estado_pedido', '')
                  ->orWhereIn('estado_pedido', ['pendiente', 'en_preparacion', 'PENDIENTE', 'EN_PREPARACION']);
            });
        } elseif ($estado === 'atendido') {
            $query->whereIn('estado_pedido', [
                'atendido', 
                'entregado', 
                'completado', 
                'ATENDIDO', 
                'ENTREGADO', 
                'COMPLETADO',
                'ACEPTADO'
            ]);
        }

        if ($request->filled('buscar')) {
            $term = $request->buscar;
            $query->whereHas('cliente', function($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('documento', 'like', "%{$term}%");
            });
        }

        if ($request->filled('fecha_inicio')) {
            $query->whereDate('created_at', '>=', $request->fecha_inicio);
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate('created_at', '<=', $request->fecha_fin);
        }

        $ventas = $query->get();

        $filename = 'ventas_ecommerce_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($ventas) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, [
                'ID',
                'Código',
                'Fecha',
                'Cliente',
                'Documento',
                'Email',
                'Teléfono',
                'Estado',
                'Método Pago',
                'Subtotal',
                'IGV',
                'Total',
                'Productos',
                'Observaciones'
            ]);
            
            foreach ($ventas as $venta) {
                $productos = $venta->detalles->map(function($detalle) {
                    return $detalle->producto->nombre . ' (x' . $detalle->cantidad . ')';
                })->implode(', ');
                
                fputcsv($file, [
                    $venta->id,
                    'ECOM-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT),
                    $venta->created_at->format('d/m/Y H:i'),
                    $venta->cliente->nombre ?? 'N/A',
                    $venta->cliente->documento ?? 'N/A',
                    $venta->cliente->email ?? 'N/A',
                    $venta->cliente->telefono ?? 'N/A',
                    ucfirst($venta->estado_pedido ?? 'pendiente'),
                    ucfirst($venta->metodo_pago ?? 'N/A'),
                    number_format($venta->subtotal, 2),
                    number_format($venta->igv, 2),
                    number_format($venta->total, 2),
                    $productos,
                    $venta->observaciones ?? ''
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ============================================
    // 📊 REPORTES DE VENTAS
    // ============================================

    public function ventasAdmin(Request $request)
    {
        $standSeleccionado = $request->get('stand', 1);
        $tipoFiltro = $request->get('tipo_filtro');
        $metodoPago = $request->get('metodo_pago');
        $estadoCredito = $request->get('estado_credito');
        $estadoReparacion = $request->get('estado_reparacion');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $buscar = $request->get('buscar');

        $ventas = collect();
        $reparaciones = collect();
        $stats = [];

        if ($standSeleccionado == 2 && $tipoFiltro === 'reparaciones') {
            $query = Reparacion::query()->orderBy('created_at', 'desc');

            if ($estadoReparacion) {
                $query->where('estado', $estadoReparacion);
            }
            if ($fechaInicio) {
                $query->whereDate('created_at', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $query->whereDate('created_at', '<=', $fechaFin);
            }
            if ($buscar) {
                $query->where(function($q) use ($buscar) {
                    $q->where('cliente_nombre', 'like', "%{$buscar}%")
                      ->orWhere('cliente_telefono', 'like', "%{$buscar}%")
                      ->orWhere('tipo_equipo', 'like', "%{$buscar}%");
                });
            }

            $reparaciones = $query->paginate(20)->appends($request->query());

            $stats = [
                'total_reparaciones' => Reparacion::count(),
                'total_ingresos' => Reparacion::sum('costo_total'),
                'pendientes' => Reparacion::whereIn('estado', ['recibido', 'diagnosticando', 'en_reparacion'])->count(),
                'completadas' => Reparacion::where('estado', 'entregado')->count(),
            ];

        } else {
            $query = Venta::with(['cliente', 'usuario'])
                          ->where('stand_id', $standSeleccionado)
                          ->orderBy('created_at', 'desc');

            if ($metodoPago) {
                $query->where('tipo_pago', $metodoPago);
            }
            if ($metodoPago === 'credito' && $estadoCredito) {
                if ($estadoCredito === 'pagado') {
                    $query->whereDoesntHave('cuotas', fn($c) => $c->where('pagada', false));
                } elseif ($estadoCredito === 'pendiente') {
                    $query->whereHas('cuotas', fn($c) => $c->where('pagada', false));
                }
            }
            if ($fechaInicio) {
                $query->whereDate('created_at', '>=', $fechaInicio);
            }
            if ($fechaFin) {
                $query->whereDate('created_at', '<=', $fechaFin);
            }
            if ($buscar) {
                $query->whereHas('cliente', fn($c) =>
                    $c->where('nombre', 'like', "%{$buscar}%")
                      ->orWhere('documento', 'like', "%{$buscar}%")
                );
            }

            $ventas = $query->paginate(20)->appends($request->query());

            $baseQuery = Venta::where('stand_id', $standSeleccionado);
            $stats = [
                'total_ventas' => $baseQuery->sum('total'),
                'cantidad_ventas' => $baseQuery->count(),
                'ventas_contado' => $baseQuery->where('tipo_pago', 'contado')->sum('total'),
                'ventas_credito' => $baseQuery->where('tipo_pago', 'credito')->sum('total'),
            ];
        }

        return view('dashboard.ventas.index', compact(
            'ventas',
            'reparaciones',
            'standSeleccionado',
            'tipoFiltro',
            'stats'
        ));
    }

    public function ventasShow($id)
    {
        $venta = Venta::with(['cliente', 'usuario', 'detalles.producto', 'cuotas'])->findOrFail($id);
        return view('dashboard.ventas.show', compact('venta'));
    }

    // ============================================
    // 📦 GESTIÓN DE ALMACÉN DESDE DASHBOARD ADMIN
    // ============================================

    public function almacenAdmin(Request $request)
    {
        $query = Producto::query();

        if ($request->filled('buscar')) {
            $term = $request->buscar;
            $query->where(function($q) use ($term) {
                $q->where('nombre', 'like', "%{$term}%")
                  ->orWhere('codigo_barras', 'like', "%{$term}%");
            });
        }

        if ($request->filled('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->filled('visible_ecommerce')) {
            $query->where('visible_ecommerce', $request->visible_ecommerce == '1');
        }

        if ($request->filled('activo')) {
            $query->where('activo', $request->activo == '1');
        }

        if ($request->filled('stock_filtro')) {
            switch ($request->stock_filtro) {
                case 'bajo':
                    $query->whereColumn('stock', '<=', 'stock_minimo');
                    break;
                case 'sin_stock':
                    $query->where('stock', 0);
                    break;
                case 'con_stock':
                    $query->where('stock', '>', 0);
                    break;
            }
        }

        $query->orderBy('nombre');
        $productos = $query->paginate(15)->withQueryString();

        $stats = [
            'total_skus' => Producto::count(),
            'total_unidades' => Producto::sum('stock'),
            'alerta_stock_bajo' => Producto::whereColumn('stock', '<=', 'stock_minimo')->count(),
            'visible_ecommerce' => Producto::where('visible_ecommerce', true)->count()
        ];

        $productos_criticos = Producto::whereColumn('stock', '<=', 'stock_minimo')
                                      ->orderBy('stock', 'asc')
                                      ->limit(10)
                                      ->get();

        $categorias = Producto::getCategorias();

        return view('dashboard.almacen', compact('productos', 'stats', 'productos_criticos', 'categorias'));
    }

    public function almacenCreate()
    {
        $categorias = Producto::getCategorias();
        return view('dashboard.almacen-create', compact('categorias'));
    }

    public function almacenStore(Request $request)
{
    $validated = $request->validate([
        'nombre' => 'required|string|max:255',
        'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras',
        'precio_compra' => 'required|numeric|min:0',
        'precio_venta' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
        'imagen' => 'nullable|image|max:2048',
        'categoria' => 'nullable|string|max:100',
        'marca' => 'nullable|string|max:100',
        'garantia_meses' => 'nullable|integer|min:0',
        'descripcion' => 'nullable|string',
        'activo' => 'nullable',
        'destacado' => 'nullable',
        'visible_ecommerce' => 'nullable'
    ]);

    $validated['activo'] = $request->has('activo') ? true : false;
    $validated['destacado'] = $request->has('destacado') ? true : false;
    $validated['visible_ecommerce'] = $request->has('visible_ecommerce') ? true : false;
    $validated['precio_venta'] = round($validated['precio_venta'] * 1.18, 2);

    if ($request->hasFile('imagen')) {
        $validated['imagen'] = \App\Helpers\ImageHelper::upload($request->file('imagen'));

    }

    $validated['user_id'] = auth()->id() ?? 1;
    Producto::create($validated);

    Cache::forget('chatbot_products_context');

    // ✅ CORREGIDO: Redirige al almacén del Dashboard Admin
    return redirect()->route('dashboard.almacen.index')
        ->with('success', '✅ Producto creado correctamente con precio + IGV.');
}

    public function almacenEdit(Producto $producto)
    {
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'producto' => $producto
            ]);
        }

        $categorias = Producto::getCategorias();
        return view('dashboard.almacen-edit', compact('producto', 'categorias'));
    }

    public function almacenUpdate(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo_barras' => 'nullable|string|max:100|unique:productos,codigo_barras,' . $producto->id,
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'imagen' => 'nullable|image|max:2048',
            'categoria' => 'nullable|string|max:100',
            'marca' => 'nullable|string|max:100',
            'garantia_meses' => 'nullable|integer|min:0',
            'descripcion' => 'nullable|string',
            'activo' => 'sometimes|boolean',
            'destacado' => 'sometimes|boolean',
            'visible_ecommerce' => 'sometimes|boolean'
        ]);

        $validated['precio_venta'] = round($validated['precio_venta'] * 1.18, 2);

                if ($request->hasFile('imagen')) {
                    if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                        Storage::disk('public')->delete($producto->imagen);
                    }
                    $validated['imagen'] = \App\Helpers\ImageHelper::upload($request->file('imagen'));
        }

        $producto->update($validated);
        Cache::forget('chatbot_products_context');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => '✅ Producto actualizado correctamente',
                'producto' => $producto->fresh()
            ]);
        }

        return redirect()->route('dashboard.almacen.index')
            ->with('success', '✅ Producto actualizado correctamente con precio + IGV.');
    }

    public function almacenDestroy(Producto $producto)
    {
        try {
            if ($producto->detalleVentas()->count() > 0) {
                return redirect()->back()
                    ->with('error', '❌ No se puede eliminar el producto porque tiene ventas registradas.');
            }

            if ($producto->imagen && Storage::disk('public')->exists($producto->imagen)) {
                Storage::disk('public')->delete($producto->imagen);
            }

            $nombreProducto = $producto->nombre;
            $producto->delete();

            Cache::forget('chatbot_products_context');

            return redirect()->route('dashboard.almacen.index')
                ->with('success', "✅ Producto '{$nombreProducto}' eliminado correctamente.");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', '❌ Error al eliminar el producto: ' . $e->getMessage());
        }
    }

    public function almacenImportar(Request $request)
    {
        $request->validate([
            'archivo_excel' => 'required|mimes:xlsx,xls,csv|max:10240'
        ]);

        try {
            $file = $request->file('archivo_excel');
            $filePath = $file->getRealPath();

            $import = new \App\Imports\ProductosImport();
            $import->import($filePath);

            $importados = $import->getImportados();
            $actualizados = $import->getActualizados();
            $errores = $import->getErrores();

            Cache::forget('chatbot_products_context');

            $mensaje = "✅ Importación completada: {$importados} productos nuevos, {$actualizados} actualizados.";
            
            if (count($errores) > 0) {
                $mensaje .= " ⚠️ " . count($errores) . " filas con errores.";
                return redirect()->route('dashboard.almacen.index')
                    ->with('warning', $mensaje)
                    ->with('errores_importacion', $errores);
            }

            return redirect()->route('dashboard.almacen.index')
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            return redirect()->route('dashboard.almacen.index')
                ->with('error', '❌ Error al importar: ' . $e->getMessage());
        }
    }

    public function almacenPlantilla()
    {
        $almacenController = new \App\Http\Controllers\AlmacenController();
        return $almacenController->descargarPlantilla();
    }

    public function almacenAjustarStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'producto_id' => 'required|exists:productos,id',
            'tipo_ajuste' => 'required|in:entrada,salida',
            'cantidad' => 'required|integer|min:1',
            'motivo' => 'required|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $producto = Producto::find($request->producto_id);

        if ($request->tipo_ajuste === 'entrada') {
            $producto->stock += $request->cantidad;
        } else {
            if ($producto->stock < $request->cantidad) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente para realizar la salida'
                ], 400);
            }
            $producto->stock -= $request->cantidad;
        }

        $producto->save();
        Cache::forget('chatbot_products_context');

        return response()->json([
            'success' => true,
            'nuevo_stock' => $producto->stock,
            'message' => '✅ Stock ajustado correctamente'
        ]);
    }

    // ============================================
    // 👥 GESTIÓN DE USUARIOS
    // ============================================

    public function usuariosIndex(Request $request)
    {
        $tipoUsuario = $request->get('tipo', 'sistema');
        $query = User::with('roles');

        if ($tipoUsuario === 'sistema') {
            $query->whereHas('roles');
        } else {
            $query->whereDoesntHave('roles');
        }

        if ($request->filled('buscar')) {
            $term = $request->buscar;
            $query->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%")
                  ->orWhere('dni', 'like', "%{$term}%")
                  ->orWhere('telefono', 'like', "%{$term}%");
            });
        }

        if ($tipoUsuario === 'sistema' && $request->filled('rol')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('name', $request->rol);
            });
        }

        if ($request->filled('active')) {
            $query->where('active', $request->active == '1');
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        if ($tipoUsuario === 'sistema') {
            $stats = [
                'total' => User::whereHas('roles')->count(),
                'activos' => User::whereHas('roles')->where('active', true)->count(),
                'inactivos' => User::whereHas('roles')->where('active', false)->count(),
                'nuevos_mes' => User::whereHas('roles')->whereMonth('created_at', Carbon::now()->month)->count(),
            ];
        } else {
            $totalClientes = User::whereDoesntHave('roles')->count();
            $clientesConCompras = User::whereDoesntHave('roles')
                ->where(function($query) {
                    $query->whereExists(function($subquery) {
                        $subquery->selectRaw(1)
                            ->from('clientes')
                            ->whereColumn('clientes.email', 'users.email')
                            ->orWhereColumn('clientes.documento', 'users.dni');
                    });
                })
                ->count();
            
            $stats = [
                'total' => $totalClientes,
                'activos' => User::whereDoesntHave('roles')->where('active', true)->count(),
                'inactivos' => User::whereDoesntHave('roles')->where('active', false)->count(),
                'nuevos_mes' => User::whereDoesntHave('roles')->whereMonth('created_at', Carbon::now()->month)->count(),
                'con_compras' => $clientesConCompras,
                'sin_compras' => $totalClientes - $clientesConCompras,
            ];
        }

        $roles = Role::all();
        return view('dashboard.usuarios.index', compact('usuarios', 'stats', 'roles', 'tipoUsuario'));
    }

    public function usuariosCreate()
    {
        $roles = Role::all();
        $stands = [1, 2];
        return view('dashboard.usuarios.create', compact('roles', 'stands'));
    }

    public function usuariosStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'dni' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'rol' => 'required|exists:roles,name',
            'stand_id' => 'nullable|in:1,2',
        ]);

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'stand_id' => $request->stand_id,
            'active' => true,
        ]);

        $usuario->assignRole($request->rol);

        return redirect()->route('dashboard.usuarios.index')
                         ->with('success', '✅ Usuario creado exitosamente');
    }

    public function usuariosShow($id)
    {
        $usuario = User::with('roles')->findOrFail($id);
        $esClienteEcommerce = $usuario->roles->isEmpty();
        $stats = null;
        
        if ($esClienteEcommerce) {
            $clienteAsociado = Cliente::where('email', $usuario->email)
                ->orWhere('documento', $usuario->dni)
                ->first();
            
            if ($clienteAsociado) {
                $stats = [
                    'total_compras' => $clienteAsociado->ventas()->count(),
                    'monto_total' => $clienteAsociado->ventas()->sum('total'),
                    'ultima_compra' => $clienteAsociado->ventas()->latest()->first(),
                    'compras_pendientes' => $clienteAsociado->ventas()
                        ->where('tipo_pago', 'credito')
                        ->where('pagado', false)
                        ->count(),
                    'tiene_deudas' => $clienteAsociado->tieneDeudas(),
                    'total_deuda' => $clienteAsociado->total_deuda,
                ];
            } else {
                $stats = [
                    'total_compras' => 0,
                    'monto_total' => 0,
                    'ultima_compra' => null,
                    'compras_pendientes' => 0,
                    'tiene_deudas' => false,
                    'total_deuda' => 0,
                ];
            }
        } elseif ($usuario->hasRole(['Stand1', 'Stand2'])) {
            $stats = [
                'ventas_totales' => Venta::where('usuario_id', $usuario->id)->count(),
                'monto_vendido' => Venta::where('usuario_id', $usuario->id)->sum('total'),
                'ultima_venta' => Venta::where('usuario_id', $usuario->id)->latest()->first(),
            ];
        }

        $roles = Role::all();
        return view('dashboard.usuarios.show', compact('usuario', 'stats', 'esClienteEcommerce', 'roles'));
    }

    public function usuariosEdit($id)
    {
        $usuario = User::with('roles')->findOrFail($id);
        $roles = Role::all();
        $stands = [1, 2];
        return view('dashboard.usuarios.edit', compact('usuario', 'roles', 'stands'));
    }

    public function usuariosUpdate(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
            'dni' => 'nullable|string|max:20',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'rol' => 'required|exists:roles,name',
            'stand_id' => 'nullable|in:1,2',
        ]);

        $usuario->update([
            'name' => $request->name,
            'email' => $request->email,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'direccion' => $request->direccion,
            'stand_id' => $request->stand_id,
        ]);

        if ($request->filled('password')) {
            $usuario->update(['password' => Hash::make($request->password)]);
        }

        $usuario->syncRoles([$request->rol]);

        return redirect()->route('dashboard.usuarios.index')
                         ->with('success', '✅ Usuario actualizado correctamente');
    }

    public function usuariosDestroy($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id == auth()->id()) {
            return redirect()->route('dashboard.usuarios.index')
                             ->with('error', '❌ No puedes eliminar tu propia cuenta');
        }

        $usuario->delete();

        return redirect()->route('dashboard.usuarios.index')
                         ->with('success', '✅ Usuario eliminado correctamente');
    }

    public function usuariosToggleEstado($id)
    {
        $usuario = User::findOrFail($id);

        if ($usuario->id == auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'No puedes desactivar tu propia cuenta'
            ]);
        }

        $usuario->active = !$usuario->active;
        $usuario->save();

        return response()->json([
            'success' => true,
            'message' => $usuario->active ? 'Usuario activado' : 'Usuario desactivado'
        ]);
    }

    // ============================================
    // 🔧 MÉTODOS AUXILIARES
    // ============================================

    private function getVentasMes()
    {
        return Venta::whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('total');
    }

    private function getVentasMensuales()
    {
        $ventas = Venta::selectRaw('MONTH(created_at) as mes, SUM(total) as total')
                       ->whereYear('created_at', Carbon::now()->year)
                       ->groupBy('mes')
                       ->pluck('total', 'mes')
                       ->toArray();

        $ventasMensuales = [];
        for ($i = 1; $i <= 12; $i++) {
            $ventasMensuales[] = $ventas[$i] ?? 0;
        }

        return $ventasMensuales;
    }

    private function getTopProductos()
    {
        return DB::table('detalle_ventas')
                 ->join('productos', 'detalle_ventas.producto_id', '=', 'productos.id')
                 ->select('productos.nombre', DB::raw('SUM(detalle_ventas.cantidad) as cantidad'))
                 ->groupBy('productos.id', 'productos.nombre')
                 ->orderByDesc('cantidad')
                 ->limit(5)
                 ->get();
    }
}