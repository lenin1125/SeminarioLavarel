<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Insertar Roles Base (no duplica si ya existen)
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Cliente', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Insertar Categorías Base (no duplica si ya existen)
        DB::table('categorias')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Deportivo', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Urbano', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Casual', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Crear Administrador si no existe
        if (!User::where('email', 'admin@sneakerslh.com')->exists()) {
            User::create([
                'rol_id' => 1,
                'nombre' => 'Admin',
                'apellido' => 'SneakersLH',
                'email' => 'admin@sneakerslh.com',
                'password' => Hash::make('admin12345'),
                'telefono' => '3001234567',
            ]);
        }

        // 4. Crear Cliente si no existe
        if (!User::where('email', 'juan@gmail.com')->exists()) {
            User::create([
                'rol_id' => 2,
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'email' => 'juan@gmail.com',
                'password' => Hash::make('cliente12345'),
                'telefono' => '3159876543',
            ]);
        }

        // 5. Insertar Productos de Prueba para el Catálogo (no duplica si ya existen)
        DB::table('productos')->insertOrIgnore([
            [
                'id' => 1,
                'nombre' => 'Nike Air Force 1',
                'descripcion' => 'Diseño clásico urbano en color blanco.',
                'precio' => 120.00,
                'stock' => 10,
                'categoria_id' => 2, // Urbano
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nombre' => 'Adidas Ultraboost',
                'descripcion' => 'Calzado deportivo con amotiguación premium.',
                'precio' => 150.00,
                'stock' => 8,
                'categoria_id' => 1, // Deportivo
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nombre' => 'Puma Suede Classic',
                'descripcion' => 'Estilo casual icónico para el día a día.',
                'precio' => 90.00,
                'stock' => 15,
                'categoria_id' => 3, // Casual
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}