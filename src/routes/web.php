<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZapatoController;

Route::get('/', function () {
    return redirect()->route('zapatos.index');
});

// Bloque 2 y 7: Registra todas las URL necesarias para el CRUD web automáticamente
Route::resource('zapatos', ZapatoController::class);