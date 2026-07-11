<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Zapato;
use App\Models\VentaDetalle;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Crea el usuario de prueba por defecto
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // 2. Ejecuta tus factorías para poblar la base de datos
        Zapato::factory(10)->create();
        VentaDetalle::factory(20)->create();
    }
}