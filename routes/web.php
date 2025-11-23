<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\ContadorController;
use App\Http\Controllers\EgresoController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;

// Controladores Stand 1
use App\Http\Controllers\Stand1\DashboardController as DashboardController1;
use App\Http\Controllers\Stand1\VentaController as VentaController1;
use App\Http\Controllers\Stand1\CuotaVentaController;

// Controladores Stand 2
use App\Http\Controllers\Stand2\DashboardController as DashboardController2;
use App\Http\Controllers\Stand2\VentaController as VentaController2;
use App\Http\Controllers\Stand2\ReparacionController as ReparacionController2;

// ============================================
// 🏠 E-COMMERCE - FRONTEND
// ============================================
Route::get('/', [ProductController::class, 'home'])->name('home');
Route::get('/home', [ProductController::class, 'home'])->name('home.page');

// ============================================
// 🔐 AUTENTICACIÓN
// ============================================
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}/{email}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/password/update', [ResetPasswordController::class, 'reset'])->name('password.update');

// ============================================
// 📂 CATEGORÍAS DEL E-COMMERCE (8 PRINCIPALES)
// ============================================
Route::get('/laptops', [ProductController::class, 'laptops'])->name('category.laptops');
Route::get('/pc-gaming', [ProductController::class, 'pcGaming'])->name('category.pc-gaming');
Route::get('/componentes', [ProductController::class, 'componentes'])->name('category.componentes');
Route::get('/perifericos', [ProductController::class, 'perifericos'])->name('category.perifericos');
Route::get('/monitores', [ProductController::class, 'monitores'])->name('category.monitores');
Route::get('/consolas', [ProductController::class, 'consolas'])->name('category.consolas');
Route::get('/accesorios', [ProductController::class, 'accesorios'])->name('category.accesorios');
Route::get('/repuestos', [ProductController::class, 'repuestos'])->name('category.repuestos');

// Array de categorías para validación
$categories = [
    'laptops', 'pc-gaming', 'componentes', 'perifericos',
    'monitores', 'consolas', 'accesorios', 'repuestos'
];

// ============================================
// 🔍 BÚSQUEDA Y PRODUCTOS
// ============================================
Route::get('/buscar', [ProductController::class, 'searchGlobal'])->name('search.global');
Route::get('/search-live', [ProductController::class, 'searchLive'])->name('search.live');

// Productos por marca
Route::get('/{category}/marca/{brand}', [ProductController::class, 'byBrand'])
    ->name('brand')
    ->where('category', implode('|', $categories))
    ->where('brand', '[a-zA-Z0-9-]+');

// ✅ DETALLE DE PRODUCTO (CORREGIDO)
Route::get('/{category}/{slug}', [ProductController::class, 'show'])
    ->name('product.show')
    ->where('category', implode('|', $categories))
    ->where('slug', '.*'); // Permite cualquier carácter en el slug

// ============================================
// 🛒 CARRITO DE COMPRAS
// ============================================
Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('cart.index');
    Route::get('/data', [CartController::class, 'getCartData'])->name('cart.data');
    Route::post('/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/update/{index}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/remove/{index}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('cart.clear');
});

// ============================================
// 💳 CHECKOUT CON STRIPE - PÚBLICO (SIN AUTH)
// ============================================
Route::prefix('checkout')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('checkout.process'); // ✅ SIN middleware auth
    Route::get('/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
    
    // Callbacks de Stripe
    Route::get('/stripe/success', [CheckoutController::class, 'stripeSuccess'])
        ->name('checkout.stripe.success');

    Route::get('/stripe/cancel', [CheckoutController::class, 'stripeCancel'])
        ->name('checkout.stripe.cancel');
    
    // 📄 Descargar comprobante (público)
    Route::get('/descargar-comprobante/{ventaId}', [CheckoutController::class, 'descargarComprobante'])
        ->name('checkout.descargarComprobante');
});

// ============================================
// 📄 PÁGINAS ESTÁTICAS
// ============================================
Route::view('/acerca-de', 'pages.about')->name('about');
Route::view('/contacto', 'pages.contact')->name('contact');
Route::view('/garantias', 'pages.warranty')->name('warranty');
Route::view('/terminos-y-condiciones', 'pages.terms')->name('terms');
Route::view('/privacidad', 'pages.privacy')->name('privacy');
Route::view('/politica-cambios-devoluciones', 'pages.returns')->name('returns');
Route::view('/libro-reclamaciones', 'pages.complaints')->name('complaints');
Route::view('/tiendas', 'pages.stores')->name('stores');

// ============================================
// 🤖 CHATBOT
// ============================================
Route::post('/chatbot', [ChatbotController::class, 'respond'])->name('chatbot.respond');

// Limpiar caché del chatbot (solo para admins)
Route::middleware(['auth'])->post('/chatbot/clear-cache', [ChatbotController::class, 'clearCache'])
    ->name('chatbot.clear-cache');

// ============================================
// 👤 RUTAS AUTENTICADAS
// ============================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/profile', function () { return view('auth.profile'); })->name('profile.edit');
    
    // 📦 MIS PEDIDOS
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
    
    // 📄 DESCARGAR COMPROBANTE - CENTRALIZADO (desde panel de usuario)
    Route::get('/orders/descargar-comprobante/{ventaId}', [OrderController::class, 'descargarComprobante'])
        ->name('orders.descargarComprobante');
});

// ============================================
// 🔥 TOGGLE E-COMMERCE - RUTA GLOBAL UNIFICADA
// (FUERA DE LOS GRUPOS - FUNCIONA PARA ADMIN Y ALMACÉN)
// ============================================
Route::middleware(['auth'])->post('/almacen/{producto}/toggle-ecommerce', [AlmacenController::class, 'toggleEcommerce'])
    ->name('almacen.toggle-ecommerce');

// ============================================
// 🛒 VENTAS E-COMMERCE - ADMINISTRADOR
// ============================================
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function() {
    Route::get('/ventas-ecommerce', [DashboardController::class, 'ventasEcommerceIndex'])
        ->name('ventas-ecommerce.index');
    
    Route::get('/ventas-ecommerce/exportar', [DashboardController::class, 'ventasEcommerceExportar'])
        ->name('ventas-ecommerce.exportar');
    
    Route::get('/ventas-ecommerce/{id}', [DashboardController::class, 'ventasEcommerceShow'])
        ->name('ventas-ecommerce.show');
    
    Route::post('/ventas-ecommerce/{id}/marcar-atendido', [DashboardController::class, 'ventasEcommerceMarcarAtendido'])
        ->name('ventas-ecommerce.marcar-atendido');
    
    Route::post('/ventas-ecommerce/{id}/marcar-pendiente', [DashboardController::class, 'ventasEcommerceMarcarPendiente'])
        ->name('ventas-ecommerce.marcar-pendiente');
    
    Route::delete('/ventas-ecommerce/{id}', [DashboardController::class, 'ventasEcommerceDestroy'])
        ->name('ventas-ecommerce.destroy');
});

// ============================================
// 🎯 RUTAS ADMIN - ACCESO COMPLETO
// ============================================
Route::middleware(['auth'])->prefix('dashboard')->name('dashboard.')->group(function () {
    // Dashboard Contador
    Route::get('/contador/dashboard', [ContadorController::class, 'dashboard'])->name('contador.dashboard');
    
    // Egresos
    Route::resource('egresos', EgresoController::class);
    
    // Proveedores
    Route::resource('proveedores', ProveedorController::class);
    
    // Reporte de Ventas Centralizado
    Route::get('/ventas', [DashboardController::class, 'ventasAdmin'])->name('ventas.index');
    Route::get('/ventas/{id}', [DashboardController::class, 'ventasShow'])->name('ventas.show');
    
    // 📦 GESTIÓN DE ALMACÉN PARA ADMIN
    Route::get('/almacen', [DashboardController::class, 'almacenAdmin'])->name('almacen.index');
    Route::get('/almacen/create', [AlmacenController::class, 'create'])->name('almacen.create');
    Route::get('/almacen/{producto}/edit', [AlmacenController::class, 'edit'])->name('almacen.edit');
    Route::post('/almacen', [DashboardController::class, 'almacenStore'])->name('almacen.store');
    Route::put('/almacen/{producto}', [DashboardController::class, 'almacenUpdate'])->name('almacen.update');
    Route::delete('/almacen/{producto}', [DashboardController::class, 'almacenDestroy'])->name('almacen.destroy');
    Route::post('/almacen/importar', [DashboardController::class, 'almacenImportar'])->name('almacen.importar');
    Route::get('/almacen/plantilla', [AlmacenController::class, 'descargarPlantilla'])->name('almacen.plantilla');

    // 👥 GESTIÓN DE USUARIOS
    Route::get('/usuarios', [DashboardController::class, 'usuariosIndex'])->name('usuarios.index');
    Route::get('/usuarios/create', [DashboardController::class, 'usuariosCreate'])->name('usuarios.create');
    Route::post('/usuarios', [DashboardController::class, 'usuariosStore'])->name('usuarios.store');
    Route::get('/usuarios/{id}', [DashboardController::class, 'usuariosShow'])->name('usuarios.show');
    Route::get('/usuarios/{id}/edit', [DashboardController::class, 'usuariosEdit'])->name('usuarios.edit'); // ✅ YA EXISTE
    Route::put('/usuarios/{id}', [DashboardController::class, 'usuariosUpdate'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [DashboardController::class, 'usuariosDestroy'])->name('usuarios.destroy');
    
    // ✅ NUEVA: Toggle estado activo/inactivo
    Route::post('/usuarios/{id}/toggle-estado', [DashboardController::class, 'usuariosToggleEstado'])
        ->name('usuarios.toggle-estado'); 
});

// ============================================
// 📦 RUTAS ALMACÉN - ROL ALMACÉN
// ============================================
Route::middleware(['auth'])->prefix('almacen')->name('almacen.')->group(function () {
    // Rutas específicas PRIMERO (antes de las resource)
    Route::get('/buscar/{codigo}', [AlmacenController::class, 'buscarPorCodigo'])->name('buscarPorCodigo');
    Route::post('/ajustar-stock', [AlmacenController::class, 'ajustarStock'])->name('ajustarStock');
    Route::post('/importar', [AlmacenController::class, 'importar'])->name('importar');
    Route::get('/plantilla', [AlmacenController::class, 'descargarPlantilla'])->name('plantilla');
    
    // Rutas CRUD básicas
    Route::get('/', [AlmacenController::class, 'index'])->name('index');
    Route::get('/create', [AlmacenController::class, 'create'])->name('create');
    Route::post('/', [AlmacenController::class, 'store'])->name('store');
    Route::get('/{producto}/edit', [AlmacenController::class, 'edit'])->name('edit');
    Route::put('/{producto}', [AlmacenController::class, 'update'])->name('update');
    Route::delete('/{producto}', [AlmacenController::class, 'destroy'])->name('destroy');
});

// ============================================
// 🏪 STAND 1 - VENTAS
// ============================================
Route::middleware(['auth'])->prefix('stand1')->name('stands.stand1.')->group(function () {
    Route::get('/dashboard', [DashboardController1::class, 'index'])->name('dashboard');
    Route::get('/ventas', [VentaController1::class, 'index'])->name('ventas.index');
    Route::get('/ventas/pos', [VentaController1::class, 'pos'])->name('ventas.pos');
    Route::get('/ventas/buscar', [VentaController1::class, 'buscar'])->name('ventas.buscar');
    Route::post('/ventas', [VentaController1::class, 'store'])->name('ventas.store');
    Route::get('/ventas/{venta}', [VentaController1::class, 'show'])->name('ventas.show');
    Route::delete('/ventas/{venta}', [VentaController1::class, 'destroy'])->name('ventas.destroy');
    Route::post('/ventas/{id}/generar-comprobante', [VentaController1::class, 'generarComprobanteYEnviar'])->name('ventas.generar-comprobante');
    Route::get('/ventas/{id}/descargar-comprobante', [VentaController1::class, 'descargarComprobante'])->name('ventas.descargar-comprobante');
    Route::post('/ventas/cuota/{cuota}/pagar', [VentaController1::class, 'pagarCuota'])->name('ventas.cuota.pagar');
    Route::post('/cuotas/{cuota}/actualizar', [CuotaVentaController::class, 'pagarCuota'])->name('cuotas.actualizar');
    Route::get('/ventas/{id}/detalle', [VentaController1::class, 'detalleModal'])->name('ventas.detalle-modal');
});

// ============================================
// 🛠️ STAND 2 - VENTAS Y REPARACIONES
// ============================================
Route::middleware(['auth'])->prefix('stand2')->name('stands.stand2.')->group(function () {
    Route::get('/dashboard', [DashboardController2::class, 'index'])->name('dashboard');
    
    // Ventas
    Route::get('/ventas', [VentaController2::class, 'index'])->name('ventas.index');
    Route::get('/ventas/pos', [VentaController2::class, 'pos'])->name('ventas.pos');
    Route::get('/ventas/buscar', [VentaController2::class, 'buscar'])->name('ventas.buscar');
    Route::post('/ventas', [VentaController2::class, 'store'])->name('ventas.store');
    Route::get('/ventas/{venta}', [VentaController2::class, 'show'])->name('ventas.show');
    Route::delete('/ventas/{venta}', [VentaController2::class, 'destroy'])->name('ventas.destroy');
    Route::post('/ventas/{id}/generar-comprobante', [VentaController2::class, 'generarComprobanteYEnviar'])->name('ventas.generar-comprobante');
    Route::get('/ventas/{id}/descargar-comprobante', [VentaController2::class, 'descargarComprobante'])->name('ventas.descargar-comprobante');
    Route::post('/ventas/cuota/{cuota}/pagar', [VentaController2::class, 'pagarCuota'])->name('ventas.cuota.pagar');
    Route::get('/ventas/{id}/detalle', [VentaController2::class, 'detalleModal'])->name('ventas.detalle-modal');
    
    // Reparaciones
    Route::get('/reparaciones', [ReparacionController2::class, 'index'])->name('reparaciones.index');
    Route::get('/reparaciones/create', [ReparacionController2::class, 'create'])->name('reparaciones.create');
    Route::post('/reparaciones', [ReparacionController2::class, 'store'])->name('reparaciones.store');
    Route::get('/reparaciones/{reparacion}', [ReparacionController2::class, 'show'])->name('reparaciones.show');
    Route::get('/reparaciones/{reparacion}/edit', [ReparacionController2::class, 'edit'])->name('reparaciones.edit');
    Route::put('/reparaciones/{reparacion}', [ReparacionController2::class, 'update'])->name('reparaciones.update');
    Route::delete('/reparaciones/{reparacion}', [ReparacionController2::class, 'destroy'])->name('reparaciones.destroy');
    Route::post('/reparaciones/{id}/generar-comprobante', [ReparacionController2::class, 'generarComprobanteYEnviar'])->name('reparaciones.generar-comprobante');
    Route::get('/reparaciones/{id}/descargar-comprobante', [ReparacionController2::class, 'descargarComprobante'])->name('reparaciones.descargar-comprobante');
    Route::get('/reparaciones/{id}/detalle', [ReparacionController2::class, 'detalleModal'])->name('reparaciones.detalle-modal');
});

// ============================================
// 🔍 RUTAS DE DEBUG (TEMPORALES - ELIMINAR EN PRODUCCIÓN)
// ============================================
Route::get('/test-auth', function() {
    return response()->json([
        'authenticated' => \Illuminate\Support\Facades\Auth::check(),
        'user_id' => \Illuminate\Support\Facades\Auth::id(),
        'user_name' => \Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::user()->name : 'Guest',
    ]);
});

Route::get('/debug-stripe/{session_id}', function($sessionId) {
    try {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        
        return response()->json([
            'session_id' => $session->id,
            'payment_status' => $session->payment_status,
            'metadata' => $session->metadata->toArray(),
            'has_order_data' => isset($session->metadata['order_data']),
            'has_cart_data' => isset($session->metadata['cart_data']),
            'order_data_length' => isset($session->metadata['order_data']) ? strlen($session->metadata['order_data']) : 0,
            'cart_data_length' => isset($session->metadata['cart_data']) ? strlen($session->metadata['cart_data']) : 0,
        ], 200, [], JSON_PRETTY_PRINT);
        
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile()
        ]);
    }
})->name('debug.stripe');

// ============================================
// 🔄 REDIRECTS (URLs antiguas a nuevas)
// ============================================
Route::redirect('/laptops-gamer', '/laptops', 301);
Route::redirect('/laptops-oficina', '/laptops', 301);
Route::redirect('/pcs-gamer', '/pc-gaming', 301);
Route::redirect('/pcs', '/pc-gaming', 301);
Route::redirect('/accesorios-gamer', '/accesorios', 301);
Route::redirect('/accessories', '/accesorios', 301);
Route::redirect('/search', '/buscar', 301);
Route::redirect('/stands', '/dashboard', 301);