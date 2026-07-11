<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ZapatoController;
use Illuminate\Support\Facades\Route;

// 1. Ruta pública para iniciar sesión y obtener el Token JWT
Route::post('login', [AuthController::class, 'login']);

// 2. Rutas PROTEGIDAS por Token JWT
Route::middleware('auth:api')->group(function () {
    
    // Ruta para cerrar sesión invalidando el Token
    Route::post('logout', [AuthController::class, 'logout']);
    
    // CRUD completo de zapatos protegido
    Route::apiResource('zapatos', ZapatoController::class);
    
});