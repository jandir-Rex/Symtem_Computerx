<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\StripePaymentService;
use App\Services\FacturacionSunatService;
use App\Services\PdfComprobanteService;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Producto;

class CheckoutController extends Controller
{
    protected $stripeService;
    protected $facturacionService;
    protected $pdfService;

    public function __construct(
        StripePaymentService $stripeService,
        FacturacionSunatService $facturacionService,
        PdfComprobanteService $pdfService
    ) {
        $this->stripeService = $stripeService;
        $this->facturacionService = $facturacionService;
        $this->pdfService = $pdfService;
    }

    /**
     * Mostrar página de checkout
     */
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        // Calcular totales
        $totalConIGV = 0;
        foreach ($cart as $item) {
            $precio = floatval($item['price'] ?? 0);
            $cantidad = $item['quantity'] ?? ($item['qty'] ?? 1);
            $totalConIGV += $precio * $cantidad;
        }

        $subtotal = round($totalConIGV / 1.18, 2);
        $igv = round($totalConIGV - $subtotal, 2);

        return view('checkout.index', [
            'cart' => $cart,
            'subtotal' => $subtotal,
            'igv' => $igv,
            'total' => $totalConIGV,
        ]);
    }

    /**
     * Procesar el pedido
     */
    public function process(Request $request)
    {
        Log::info('🔍 DEBUG CHECKOUT PROCESS', [
            'auth_check' => Auth::check(),
            'user_id' => Auth::id(),
            'form_data' => [
                'name' => $request->input('name'),
                'email' => $request->input('email'),
                'dni' => $request->input('dni'),
            ]
        ]);

        // Validar datos
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string',
            'dni' => 'nullable|string|max:20',
            'address' => 'required|string',
            'district' => 'required|string',
            'city' => 'required|string',
            'payment_method' => 'required|in:stripe,yape,plin,transfer,delivery',
            'terms' => 'required|accepted',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_holder' => 'nullable|string',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Tu carrito está vacío');
        }

        // Si es Stripe, crear sesión y redirigir
        if ($validated['payment_method'] === 'stripe') {
            return $this->processStripePayment($request, $validated, $cart);
        }

        // Para otros métodos, crear el pedido normalmente
        return $this->processOtherPayment($request, $validated, $cart);
    }

    /**
     * Procesar pago con Stripe
     */
    protected function processStripePayment(Request $request, array $validated, array $cart)
    {
        try {
            // Preparar datos para Stripe
            $items = [];
            foreach ($cart as $item) {
                $items[] = [
                    'name' => $item['name'] ?? 'Producto',
                    'description' => 'Producto de Company Computer',
                    'price' => floatval($item['price'] ?? 0),
                    'quantity' => $item['quantity'] ?? ($item['qty'] ?? 1),
                ];
            }

            $orderData = [
                'items' => $items,
                'customer' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'dni' => $validated['dni'] ?? '',
                    'address' => $validated['address'],
                    'district' => $validated['district'],
                    'city' => $validated['city'],
                ],
            ];

            // Guardar datos temporales en sesión (backup)
            session([
                'pending_order' => $orderData,
                'pending_cart' => $cart,
                'stripe_checkout_timestamp' => now()->timestamp,
            ]);

            Log::info('📦 Datos guardados en sesión (backup)', [
                'has_order' => !empty($orderData),
                'has_cart' => !empty($cart),
                'items_count' => count($cart),
            ]);

            // Crear sesión de Stripe
            $result = $this->stripeService->createCheckoutSession($orderData);

            if (!$result['success']) {
                return back()->with('error', 'Error al crear sesión de pago: ' . $result['error']);
            }

            session(['stripe_session_id' => $result['session_id'] ?? null]);

            Log::info('✅ Redirigiendo a Stripe', [
                'session_id' => $result['session_id'],
                'url' => $result['url'],
            ]);

            return redirect($result['url']);

        } catch (\Exception $e) {
            Log::error('❌ Error en processStripePayment: ' . $e->getMessage());
            return back()->with('error', 'Error al procesar el pago con tarjeta');
        }
    }

    /**
     * 🔥 Success callback de Stripe - CORREGIDO
     */
    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId) {
            Log::error('❌ No se recibió session_id en callback');
            return view('checkout.error')->with('error', 'No se recibió información del pago');
        }

        Log::info('✅ Stripe callback recibido', [
            'session_id' => $sessionId,
            'user_authenticated' => Auth::check(),
            'user_id' => Auth::id(),
            'full_url' => $request->fullUrl(),
            'all_query_params' => $request->query(),
        ]);

        try {
            // ✅ Verificar pago
            $payment = $this->stripeService->verifyPayment($sessionId);

            Log::info('🔍 Payment verification result', [
                'success' => $payment['success'] ?? false,
                'status' => $payment['status'] ?? 'unknown',
                'has_metadata' => isset($payment['metadata']),
                'metadata_keys' => isset($payment['metadata']) ? array_keys($payment['metadata']) : [],
            ]);

            if (!$payment['success']) {
                Log::error('❌ Payment verification failed', ['payment' => $payment]);
                return view('checkout.error')->with('error', 'Error al verificar el pago: ' . ($payment['error'] ?? 'Desconocido'));
            }

            if ($payment['status'] !== 'paid') {
                Log::warning('⚠️ Pago no completado', ['status' => $payment['status']]);
                return view('checkout.error')->with('error', 'El pago no se completó correctamente');
            }

            // 🔥 RECUPERAR DATOS
            $metadata = $payment['metadata'];
            
            Log::info('📦 Metadata recibido', [
                'keys' => array_keys($metadata),
                'has_order_data' => isset($metadata['order_data']),
                'has_cart_data' => isset($metadata['cart_data']),
                'order_data_length' => isset($metadata['order_data']) ? strlen($metadata['order_data']) : 0,
                'cart_data_length' => isset($metadata['cart_data']) ? strlen($metadata['cart_data']) : 0,
            ]);

            $orderData = null;
            $cart = null;

            // Intentar decodificar order_data
            if (!empty($metadata['order_data'])) {
                try {
                    $orderData = json_decode($metadata['order_data'], true);
                    $jsonError = json_last_error();
                    
                    Log::info('🔄 Decodificando order_data', [
                        'success' => $jsonError === JSON_ERROR_NONE,
                        'json_error' => $jsonError,
                        'json_error_msg' => json_last_error_msg(),
                        'has_customer' => isset($orderData['customer']),
                        'has_items' => isset($orderData['items']),
                    ]);
                    
                    if ($jsonError !== JSON_ERROR_NONE) {
                        Log::error('❌ Error al decodificar order_data JSON', [
                            'error' => json_last_error_msg(),
                            'raw_data' => substr($metadata['order_data'], 0, 200)
                        ]);
                        $orderData = null;
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Excepción al decodificar order_data', [
                        'error' => $e->getMessage()
                    ]);
                    $orderData = null;
                }
            }

            // Intentar decodificar cart_data
            if (!empty($metadata['cart_data'])) {
                try {
                    $cart = json_decode($metadata['cart_data'], true);
                    $jsonError = json_last_error();
                    
                    Log::info('🔄 Decodificando cart_data', [
                        'success' => $jsonError === JSON_ERROR_NONE,
                        'json_error' => $jsonError,
                        'json_error_msg' => json_last_error_msg(),
                        'items_count' => is_array($cart) ? count($cart) : 0,
                    ]);
                    
                    if ($jsonError !== JSON_ERROR_NONE) {
                        Log::error('❌ Error al decodificar cart_data JSON', [
                            'error' => json_last_error_msg(),
                            'raw_data' => substr($metadata['cart_data'], 0, 200)
                        ]);
                        $cart = null;
                    }
                } catch (\Exception $e) {
                    Log::error('❌ Excepción al decodificar cart_data', [
                        'error' => $e->getMessage()
                    ]);
                    $cart = null;
                }
            }

            // Reconstruir desde campos individuales
            if (!$orderData || !isset($orderData['customer'])) {
                Log::info('📦 Reconstruyendo orderData desde campos individuales');
                
                $orderData = [
                    'customer' => [
                        'name' => $metadata['customer_name'] ?? 'Cliente',
                        'email' => $metadata['customer_email'] ?? '',
                        'phone' => $metadata['customer_phone'] ?? '',
                        'dni' => $metadata['customer_dni'] ?? '',
                        'address' => $metadata['customer_address'] ?? '',
                        'district' => $metadata['customer_district'] ?? '',
                        'city' => $metadata['customer_city'] ?? '',
                    ],
                    'items' => $cart ?? [],
                ];
                
                Log::info('✅ OrderData reconstruido', [
                    'customer' => $orderData['customer'],
                    'items_count' => count($orderData['items']),
                ]);
            }

            // Backup: sesión
            if (!$cart || empty($cart)) {
                $cart = session('pending_cart', []);
                Log::info('🔄 Cart recuperado desde sesión', [
                    'items_count' => count($cart),
                    'session_exists' => session()->has('pending_cart'),
                ]);
            }

            // Usar items de orderData si cart vacío
            if (empty($cart) && isset($orderData['items'])) {
                $cart = $orderData['items'];
                Log::info('🔄 Cart tomado desde orderData->items');
            }

            // Validar
            Log::info('🔍 Validando datos finales', [
                'has_orderData' => !empty($orderData),
                'has_customer' => isset($orderData['customer']),
                'customer_name' => $orderData['customer']['name'] ?? 'N/A',
                'customer_email' => $orderData['customer']['email'] ?? 'N/A',
                'cart_count' => is_array($cart) ? count($cart) : 0,
                'cart_items' => is_array($cart) ? array_map(fn($i) => $i['name'] ?? 'sin nombre', $cart) : [],
            ]);

            if (!$orderData || !isset($orderData['customer']) || empty($cart)) {
                Log::error('❌ DATOS INSUFICIENTES PARA CREAR PEDIDO', [
                    'has_orderData' => !empty($orderData),
                    'has_customer' => isset($orderData['customer']),
                    'cart_count' => is_array($cart) ? count($cart) : 0,
                    'metadata_keys' => array_keys($metadata),
                    'session_data' => [
                        'has_pending_order' => session()->has('pending_order'),
                        'has_pending_cart' => session()->has('pending_cart'),
                    ],
                ]);

                return view('checkout.error')->with('error', 
                    '⚠️ El pago fue recibido pero faltan datos para procesar el pedido. ' .
                    'Por favor contacta a soporte con este código: ' . substr($sessionId, -12)
                );
            }

            // ✅ Crear pedido
            Log::info('✅ Todos los datos validados. Creando pedido...');
            return $this->crearPedidoFinal($sessionId, $payment, $orderData, $cart);

        } catch (\Exception $e) {
            Log::error('❌ EXCEPCIÓN CRÍTICA en stripeSuccess', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return view('checkout.error')->with('error', 
                '❌ Error procesando el pago. Código: ' . substr($sessionId ?? 'N/A', -12)
            );
        }
    }

    /**
     * Cancel callback de Stripe
     */
    public function stripeCancel()
    {
        Log::info('Usuario canceló el pago en Stripe');
        return redirect()->route('checkout.index')->with('info', 'El pago fue cancelado.');
    }

    /**
     * Página de cancelación general
     */
    public function cancel()
    {
        return redirect()->route('checkout.index')->with('error', 'El proceso fue cancelado.');
    }

    /**
     * Procesar otros métodos de pago (Yape, Plin, etc.)
     */
    protected function processOtherPayment(Request $request, array $validated, array $cart)
    {
        DB::beginTransaction();

        try {
            $documento = $validated['dni'] ?? '';

            // ✅ SIEMPRE USAR DATOS DEL FORMULARIO ($validated)
            $cliente = Cliente::firstOrCreate(
                ['documento' => $documento ?: 'SIN-DOC-' . time()],
                [
                    'nombre' => $validated['name'],
                    'email' => $validated['email'],
                    'telefono' => $validated['phone'],
                    'direccion' => $validated['address'] . ', ' . $validated['district'] . ', ' . $validated['city'],
                ]
            );

            Log::info('✅ Cliente creado/encontrado', [
                'cliente_id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'documento' => $cliente->documento,
                'email' => $cliente->email
            ]);

            // Calcular totales
            $totalConIGV = 0;
            foreach ($cart as $item) {
                $precio = floatval($item['price'] ?? 0);
                $cantidad = $item['quantity'] ?? ($item['qty'] ?? 1);
                $totalConIGV += $precio * $cantidad;
            }

            $subtotal = round($totalConIGV / 1.18, 2);
            $igv = round($totalConIGV - $subtotal, 2);

            $tipoComprobante = (strlen($documento) === 11) ? 'FACTURA' : 'BOLETA';

            // Determinar user_id: null si es cliente/invitado
            $userId = null;
            if (Auth::check()) {
                $userRole = Auth::user()->role ?? 'cliente';
                if (!in_array(strtolower($userRole), ['cliente', 'customer', 'user'])) {
                    $userId = Auth::id();
                }
            }

            // 🔥 PREPARAR METADATA CON TODA LA INFO
            $metadata = [
                'payment_method' => $validated['payment_method'],
                'customer_data' => [
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'phone' => $validated['phone'],
                    'dni' => $validated['dni'] ?? '',
                    'address' => $validated['address'],
                    'district' => $validated['district'],
                    'city' => $validated['city'],
                ],
                'cart_data' => $cart,
                'transaction_date' => now()->toIso8601String(),
            ];

            // Agregar datos bancarios si aplica
            if ($validated['payment_method'] === 'transfer') {
                $metadata['bank_info'] = [
                    'bank_name' => $validated['bank_name'] ?? null,
                    'account_number' => $validated['account_number'] ?? null,
                    'account_holder' => $validated['account_holder'] ?? null,
                ];
            }

            // 🔥 CREAR VENTA - OBSERVACIONES CORTAS, METADATA COMPLETO
            $venta = Venta::create([
                'cliente_id' => $cliente->id,
                'user_id' => $userId,
                'stand_id' => null, // ✅ NULLABLE
                'tipo_comprobante' => $tipoComprobante,
                'tipo_pago' => 'contado',
                'metodo_pago' => $validated['payment_method'], 
                'pagado' => false,
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $totalConIGV,
                'observaciones' => 'Pago: ' . strtoupper($validated['payment_method']),
                'metadata' => $metadata,
                'estado_sunat' => 'PENDIENTE',
                'estado_pedido' => 'pendiente',
            ]);

            Log::info('✅ Venta creada', [
                'venta_id' => $venta->id,
                'cliente_id' => $venta->cliente_id,
                'total' => $venta->total
            ]);

            // 🔥 Crear detalles con búsqueda por nombre
            foreach ($cart as $item) {
                $productoId = $item['id'] ?? null;
                $precio = floatval($item['price'] ?? 0);
                $cantidad = $item['quantity'] ?? ($item['qty'] ?? 1);

                // 🔥 Si no hay producto_id, buscar por nombre
                if (!$productoId && !empty($item['name'])) {
                    $producto = Producto::where('nombre', $item['name'])->first();
                    if ($producto) {
                        $productoId = $producto->id;
                        Log::info('✅ Producto encontrado por nombre', [
                            'nombre' => $item['name'],
                            'id' => $productoId
                        ]);
                    } else {
                        Log::warning('⚠️ Producto no encontrado en BD', [
                            'nombre' => $item['name']
                        ]);
                    }
                }

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $productoId, // Puede ser null
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $precio * $cantidad,
                ]);

                // Descontar stock solo si hay producto_id válido
                if ($productoId) {
                    $producto = Producto::find($productoId);
                    if ($producto && $producto->stock >= $cantidad) {
                        $producto->decrement('stock', $cantidad);
                        Log::info('📦 Stock descontado', [
                            'producto_id' => $producto->id,
                            'nombre' => $producto->nombre,
                            'cantidad' => $cantidad,
                            'stock_restante' => $producto->stock
                        ]);
                    }
                }
            }

            DB::commit();

            // Limpiar carrito
            session()->forget('cart');

            return redirect()->route('checkout.success')->with([
                'success' => 'Pedido realizado exitosamente',
                'order' => [
                    'id' => $venta->id,
                    'code' => 'PED-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT),
                    'total' => $venta->total,
                    'subtotal' => $venta->subtotal,
                    'igv' => $venta->igv,
                    'customer' => $validated,
                    'items' => $cart,
                ],
                'download_venta_id' => $venta->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en processOtherPayment', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al procesar el pedido: ' . $e->getMessage());
        }
    }

    /**
     * 🔥 Crea el pedido final después de verificación exitosa de Stripe
     */
    protected function crearPedidoFinal(string $sessionId, array $payment, array $orderData, array $cart)
    {
        // Verificar idempotencia usando metadata
        $existingVenta = Venta::whereJsonContains('metadata->stripe_session_id', $sessionId)->first();

        if ($existingVenta) {
            Log::info('✅ Pedido ya procesado (idempotencia)', ['venta_id' => $existingVenta->id]);

            session()->forget(['cart', 'pending_order', 'pending_cart', 'stripe_checkout_timestamp', 'stripe_session_id']);

            return redirect()->route('checkout.success')->with([
                'success' => '¡Pago exitoso! Pedido #' . $existingVenta->id,
                'order' => [
                    'id' => $existingVenta->id,
                    'code' => $existingVenta->numero_comprobante ?? 'PED-' . str_pad($existingVenta->id, 6, '0', STR_PAD_LEFT),
                    'total' => $existingVenta->total,
                ],
                'download_venta_id' => $existingVenta->id
            ]);
        }

        DB::beginTransaction();

        try {
            // ✅ USAR DATOS DEL FORMULARIO (orderData)
            $clienteData = $orderData['customer'];
            $documento = $clienteData['dni'] ?? '';

            // ✅ CREAR CLIENTE CON DATOS DEL FORMULARIO
            $cliente = Cliente::firstOrCreate(
                ['documento' => $documento ?: 'STRIPE-' . time()],
                [
                    'nombre' => $clienteData['name'],
                    'email' => $clienteData['email'],
                    'telefono' => $clienteData['phone'],
                    'direccion' => $clienteData['address'] . ', ' . $clienteData['district'] . ', ' . $clienteData['city'],
                ]
            );

            Log::info('✅ Cliente Stripe creado/encontrado', [
                'cliente_id' => $cliente->id,
                'nombre' => $cliente->nombre,
                'documento' => $cliente->documento,
                'email' => $cliente->email
            ]);

            // Calcular totales
            $totalConIGV = 0;
            foreach ($cart as $item) {
                $precio = floatval($item['price'] ?? 0);
                $cantidad = $item['quantity'] ?? ($item['qty'] ?? 1);
                $totalConIGV += $precio * $cantidad;
            }

            $subtotal = round($totalConIGV / 1.18, 2);
            $igv = round($totalConIGV - $subtotal, 2);

            $tipoComprobante = (strlen($documento) === 11) ? 'FACTURA' : 'BOLETA';

            // Determinar user_id
            $userId = null;
            if (Auth::check()) {
                $userRole = Auth::user()->role ?? 'cliente';
                if (!in_array(strtolower($userRole), ['cliente', 'customer', 'user'])) {
                    $userId = Auth::id();
                }
            }

            // 🔥 PREPARAR METADATA COMPLETO CON TODA LA INFORMACIÓN
            $metadata = [
                'payment_method' => 'stripe',
                'stripe_session_id' => $sessionId,
                'stripe_payment_intent' => $payment['payment_intent_id'] ?? null,
                'stripe_charge_id' => $payment['charge_id'] ?? null,
                'stripe_amount' => $payment['amount'] ?? null,
                'stripe_currency' => $payment['currency'] ?? 'pen',
                'stripe_status' => $payment['status'] ?? null,
                'customer_data' => [
                    'name' => $clienteData['name'],
                    'email' => $clienteData['email'],
                    'phone' => $clienteData['phone'],
                    'dni' => $clienteData['dni'] ?? '',
                    'address' => $clienteData['address'],
                    'district' => $clienteData['district'],
                    'city' => $clienteData['city'],
                ],
                'cart_data' => $cart,
                'items_data' => $orderData['items'] ?? [],
                'transaction_date' => now()->toIso8601String(),
                'payment_completed_at' => now()->toIso8601String(),
            ];

            // 🔥 OBSERVACIONES CORTAS (máximo 50 caracteres)
            $sessionCorto = substr($sessionId, -12);
            $observacionesCortas = "Stripe: {$sessionCorto}";

            // 🔥 CREAR VENTA
            $venta = Venta::create([
                'cliente_id' => $cliente->id,
                'user_id' => $userId,
                'stand_id' => null, // ✅ NULLABLE
                'tipo_comprobante' => $tipoComprobante,
                'tipo_pago' => 'contado',
                'metodo_pago' => 'tarjeta', 
                'pagado' => true,
                'fecha_pago' => now(),
                'subtotal' => $subtotal,
                'igv' => $igv,
                'total' => $totalConIGV,
                'observaciones' => $observacionesCortas,
                'metadata' => $metadata,
                'estado_sunat' => 'PENDIENTE',
                'estado_pedido' => 'pendiente',
            ]);

            Log::info('✅ Venta Stripe creada', [
                'venta_id' => $venta->id,
                'cliente_id' => $venta->cliente_id,
                'total' => $venta->total,
                'estado_pedido' => $venta->estado_pedido,
                'metadata_stored' => true
            ]);

            // 🔥 Crear detalles con búsqueda por nombre
            foreach ($cart as $item) {
                $productoId = $item['id'] ?? null;
                $precio = floatval($item['price'] ?? 0);
                $cantidad = $item['quantity'] ?? ($item['qty'] ?? 1);

                // 🔥 Si no hay producto_id, buscar por nombre
                if (!$productoId && !empty($item['name'])) {
                    $producto = Producto::where('nombre', $item['name'])->first();
                    if ($producto) {
                        $productoId = $producto->id;
                        Log::info('✅ Producto encontrado por nombre', [
                            'nombre' => $item['name'],
                            'id' => $productoId
                        ]);
                    } else {
                        Log::warning('⚠️ Producto no encontrado en BD', [
                            'nombre' => $item['name']
                        ]);
                    }
                }

                DetalleVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $productoId, // Puede ser null
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $precio * $cantidad,
                ]);

                // Descontar stock solo si hay producto_id válido
                if ($productoId) {
                    $producto = Producto::find($productoId);
                    if ($producto && $producto->stock >= $cantidad) {
                        $producto->decrement('stock', $cantidad);
                        Log::info('📦 Stock descontado', [
                            'producto_id' => $producto->id,
                            'nombre' => $producto->nombre,
                            'cantidad' => $cantidad,
                            'stock_restante' => $producto->stock
                        ]);
                    }
                }
            }

            // Generar comprobante SUNAT
            try {
                $resultSunat = $this->facturacionService->generarComprobante($venta);
            } catch (\Exception $e) {
                Log::error('Error SUNAT', ['error' => $e->getMessage()]);
            }

            DB::commit();

            // Limpiar sesión
            session()->forget(['cart', 'pending_order', 'pending_cart', 'stripe_checkout_timestamp', 'stripe_session_id']);

            return redirect()->route('checkout.success')->with([
                'success' => '¡Pago exitoso! Pedido #' . $venta->id,
                'order' => [
                    'id' => $venta->id,
                    'code' => $venta->numero_comprobante ?? 'PED-' . str_pad($venta->id, 6, '0', STR_PAD_LEFT),
                    'total' => $venta->total,
                    'subtotal' => $venta->subtotal,
                    'igv' => $venta->igv,
                    'customer' => $clienteData,
                    'items' => $cart,
                ],
                'download_venta_id' => $venta->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('❌ Error al crear pedido Stripe', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return view('checkout.error')->with('error', 'Error al procesar. Código: ' . substr($sessionId, -10));
        }
    }

    /**
     * Página de éxito
     */
    public function success()
    {
        return view('checkout.success');
    }

    /**
     * Descarga del comprobante PDF
     */
    public function descargarComprobante(int $ventaId)
    {
        try {
            $venta = Venta::with('cliente')->findOrFail($ventaId);

            // Generar o recuperar el PDF
            $filePath = $this->pdfService->generarYGuardar($venta);

            if (!Storage::disk('public')->exists($filePath)) {
                return response()->json(['error' => 'PDF no encontrado'], 404);
            }

            $nombreArchivo = basename($filePath);
            return Storage::disk('public')->download($filePath, $nombreArchivo);

        } catch (\Exception $e) {
            Log::error('Error al descargar comprobante', [
                'venta_id' => $ventaId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Error al generar el comprobante'
            ], 500);
        }
    }
}