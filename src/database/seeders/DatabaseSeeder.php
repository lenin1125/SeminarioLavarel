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
        // 1. Insertar Roles Base (Seguro: insertOrIgnore evita duplicados)
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Cliente', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Insertar Categorías Base
        DB::table('categorias')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Deportivo', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Urbano', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 3, 'nombre' => 'Casual', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Insertar Tallas Base (36 a 44) de forma limpia
        $tallas = [36, 37, 38, 39, 40, 41, 42, 43, 44];

        // Usamos updateOrInsert en lugar de truncate para no romper las relaciones (llaves foráneas)
        foreach ($tallas as $talla) {
            DB::table('tallas')->updateOrInsert(
                ['numero' => $talla], // Busca si ya existe este número
                ['created_at' => now(), 'updated_at' => now()] // Si no existe, lo crea
            );
        }

        // 4. Crear Administrador (firstOrCreate es más limpio y propio de Laravel)
        User::firstOrCreate(
            ['email' => 'admin@sneakerslh.com'], // Condición de búsqueda
            [
                'rol_id' => 1,
                'nombre' => 'Admin',
                'apellido' => 'SneakersLH',
                'password' => Hash::make('admin12345'),
                'telefono' => '3001234567',
            ] // Datos a crear si no lo encuentra
        );

        // 5. Crear Cliente de prueba
        User::firstOrCreate(
            ['email' => 'juan@gmail.com'],
            [
                'rol_id' => 2,
                'nombre' => 'Juan',
                'apellido' => 'Perez',
                'password' => Hash::make('cliente12345'),
                'telefono' => '3159876543',
            ]
        );
    }
}