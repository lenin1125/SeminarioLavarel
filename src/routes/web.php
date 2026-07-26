<?php

use Illuminate\Support\Facades\Route;

// Importación de Controladores
use App\Http\Controllers\TiendaController; 
use App\Http\Controllers\CarritoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ZapatoController;
use App\Http\Controllers\Admin\PedidoController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Middleware\IsAdminMiddleware;

/*
|--------------------------------------------------------------------------
| 1. Rutas Públicas (Catálogo y Tienda)
|--------------------------------------------------------------------------
*/
Route::get('/', [TiendaController::class, 'index'])->name('tienda.index');
Route::get('/zapatos/{id}', [TiendaController::class, 'show'])->name('tienda.show');

/*
|--------------------------------------------------------------------------
| 2. Sistema de Autenticación
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| 3. Rutas del Carrito (Públicas: Clientes e Invitados)
|--------------------------------------------------------------------------
*/
Route::get('/carrito', [CarritoController::class, 'index'])->name('carrito.index');
Route::post('/carrito/agregar/{id}', [CarritoController::class, 'agregar'])->name('carrito.agregar');
Route::post('/carrito/actualizar/{id}', [CarritoController::class, 'actualizar'])->name('carrito.actualizar');

/*
|--------------------------------------------------------------------------
| 4. Rutas de Checkout (Protegidas: Requieren Inicio de Sesión)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/procesar', [CheckoutController::class, 'procesar'])->name('checkout.procesar');
    Route::get('/checkout/adjuntar-pago', [CheckoutController::class, 'pagoPantalla'])->name('checkout.pago_pantalla');
    Route::post('/checkout/guardar-pago', [CheckoutController::class, 'guardarPago'])->name('checkout.guardar_pago');
});

/*
|--------------------------------------------------------------------------
| 5. Panel Administrativo (Protegido por Autenticación y Rol Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', IsAdminMiddleware::class])->group(function () {
    // Zapatos (Productos)
    Route::get('/zapatos', [ZapatoController::class, 'index'])->name('admin.zapatos.index');
    Route::get('/zapatos/crear', [ZapatoController::class, 'create'])->name('admin.zapatos.create');
    Route::post('/zapatos', [ZapatoController::class, 'store'])->name('admin.zapatos.store');
    Route::get('/zapatos/{id}/editar', [ZapatoController::class, 'edit'])->name('admin.zapatos.edit');
    Route::put('/zapatos/{id}', [ZapatoController::class, 'update'])->name('admin.zapatos.update');
    Route::delete('/zapatos/{id}', [ZapatoController::class, 'destroy'])->name('admin.zapatos.destroy');
    Route::patch('zapatos/{id}/toggle', [ZapatoController::class, 'toggleEstado'])->name('admin.zapatos.toggle');

    // Proveedores
    Route::get('/proveedores', [DashboardController::class, 'proveedoresIndex'])->name('admin.proveedores.index');
    Route::post('/proveedores', [DashboardController::class, 'proveedoresStore'])->name('admin.proveedores.store');

    // Pagos y Pedidos
    Route::get('/pagos', [PedidoController::class, 'pagosIndex'])->name('admin.pagos.index');
    Route::post('/pedidos/aprobar/{pedido_id}', [PedidoController::class, 'aprobar'])->name('admin.pedidos.aprobar');
    Route::delete('/pedidos/{id}/rechazar', [PedidoController::class, 'rechazar'])->name('admin.pedidos.rechazar');
    Route::get('/pedidos-confirmados', [PedidoController::class, 'confirmadosIndex'])->name('admin.pedidos.confirmados');

    // Reportes y Usuarios
    Route::get('/reportes', [DashboardController::class, 'reportes'])->name('admin.reportes.index');
    Route::get('/usuarios', [DashboardController::class, 'usuariosIndex'])->name('admin.usuarios.index');
});

Route::get('/ejecutar-migracion-secret-123', function () {
    \Illuminate\Support\Facades\Artisan::call('migrate --force');
    return '¡Migración ejecutada con éxito!<br><pre>' . \Illuminate\Support\Facades\Artisan::output() . '</pre>';
});