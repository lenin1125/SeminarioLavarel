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
Route::controller(TiendaController::class)->group(function () {
    Route::get('/', 'index')->name('tienda.index');
    Route::get('/zapatos/{id}', 'show')->name('tienda.show');
});

/*
|--------------------------------------------------------------------------
| 2. Sistema de Autenticación
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login');
    Route::get('/registro', 'showRegister')->name('register');
    Route::post('/registro', 'register')->name('register.store');
    Route::post('/logout', 'logout')->name('logout');
});

/*
|--------------------------------------------------------------------------
| 3. Carrito de Compras (Público)
|--------------------------------------------------------------------------
*/
Route::controller(CarritoController::class)->prefix('carrito')->name('carrito.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/agregar/{id}', 'agregar')->name('agregar');
    Route::post('/actualizar/{id}', 'actualizar')->name('actualizar');
});

/*
|--------------------------------------------------------------------------
| 4. Proceso de Checkout (Protegido por Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->controller(CheckoutController::class)->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/procesar', 'procesar')->name('procesar');
    Route::get('/adjuntar-pago', 'pagoPantalla')->name('pago_pantalla');
    Route::post('/guardar-pago', 'guardarPago')->name('guardar_pago');
});

/*
|--------------------------------------------------------------------------
| 5. Panel Administrativo (Requiere Autenticación y Rol Admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', IsAdminMiddleware::class])->name('admin.')->group(function () {

    // Gestión de Inventario (Tenis / Zapatos)
    Route::controller(ZapatoController::class)->prefix('zapatos')->name('zapatos.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/crear', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/{id}/editar', 'edit')->name('edit');
        Route::put('/{id}', 'update')->name('update');
        Route::delete('/{id}', 'destroy')->name('destroy');
        Route::patch('/{id}/toggle', 'toggleEstado')->name('toggle');
    });

    // Gestión de Pedidos y Pagos
    Route::controller(PedidoController::class)->group(function () {
        Route::get('/pagos', 'pagosIndex')->name('pagos.index');
        Route::get('/pedidos', 'index')->name('pedidos.index');
        Route::get('/pedidos-confirmados', 'index')->name('pedidos.confirmados');
        Route::get('/pedidos/exportar-pdf', 'exportarPdf')->name('pedidos.exportar_pdf');
        Route::post('/pedidos/aprobar/{pedido_id}', 'aprobar')->name('pedidos.aprobar');
        Route::delete('/pedidos/{id}/rechazar', 'rechazar')->name('pedidos.rechazar');
    });

    // Módulos del Dashboard (Proveedores, Reportes, Usuarios)
    Route::controller(DashboardController::class)->group(function () {
        Route::get('/reportes', 'reportes')->name('reportes.index');
        Route::get('/usuarios', 'usuariosIndex')->name('usuarios.index');
    });

});