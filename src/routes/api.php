<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductoController;
use App\Http\Controllers\Api\ZapatoController;
use App\Http\Controllers\Api\PedidoController;
use App\Http\Controllers\Api\PagoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Cualquiera las puede consultar)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Catálogo de Productos y Zapatos
Route::get('/productos', [ProductoController::class, 'index']);
Route::get('/productos/{id}', [ProductoController::class, 'show']);

Route::get('/zapatos', [ZapatoController::class, 'index']);
Route::get('/zapatos/{id}', [ZapatoController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Login / Token Sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {
    
    // Acciones generales del usuario autenticado
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/user', [AuthController::class, 'me']); // Alias por compatibilidad

    // Pedidos y Pagos del Cliente
    Route::get('/pedidos', [PedidoController::class, 'index']);
    Route::post('/pedidos', [PedidoController::class, 'store']);
    Route::post('/pagos/registrar', [PagoController::class, 'registrarPago']);

    /*
    |----------------------------------------------------------------------
    | Rutas Exclusivas de Administrador (Requieren Middleware 'admin')
    |----------------------------------------------------------------------
    */
    Route::middleware('admin')->group(function () {
        
        // Gestión de Productos
        Route::post('/productos', [ProductoController::class, 'store']);
        Route::put('/productos/{id}', [ProductoController::class, 'update']);
        Route::delete('/productos/{id}', [ProductoController::class, 'destroy']);

        // Gestión de Zapatos (Alias)
        Route::post('/zapatos', [ZapatoController::class, 'store']);
        Route::put('/zapatos/{id}', [ZapatoController::class, 'update']);
        Route::delete('/zapatos/{id}', [ZapatoController::class, 'destroy']);

        // Validación de Pagos (Acepta PUT o POST)
        Route::put('/pagos/{id}/validar', [PagoController::class, 'validarPago']);
        Route::post('/pagos/{id}/validar', [PagoController::class, 'validarPago']);
    });
});