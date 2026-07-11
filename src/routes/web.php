<?php

use App\Http\Controllers\ZapatoController;
use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('zapatos.index');
});

// Rutas del CRUD de Zapatos
Route::get('/zapatos', [ZapatoController::class, 'index'])->name('zapatos.index');
Route::get('/zapatos/{zapato}/edit', [ZapatoController::class, 'edit'])->name('zapatos.edit');
Route::put('/zapatos/{zapato}', [ZapatoController::class, 'update'])->name('zapatos.update');
Route::delete('/zapatos/{zapato}', [ZapatoController::class, 'destroy'])->name('zapatos.destroy');

// Rutas de creación exclusivas para administradores
Route::middleware('rol:admin')->group(function () {
    Route::get('/zapatos/create', [ZapatoController::class, 'create'])->name('zapatos.create');
    Route::post('/zapatos', [ZapatoController::class, 'store'])->name('zapatos.store');
});

// NUEVA RUTA: Consumo de API externa de la Actividad 4
Route::get('/posts', [PostsController::class, 'index'])->name('posts.index');