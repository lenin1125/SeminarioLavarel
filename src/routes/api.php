<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZapatoController; // <-- 1. Importamos tu controlador

// Esta ruta viene por defecto para la autenticación
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// --- 2. Tu ruta del CRUD de Zapatos ---
Route::apiResource('zapatos', ZapatoController::class);