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
        // 1. Insertar Roles Base
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

// Limpiamos la tabla primero o borramos duplicados si los hay
DB::table('tallas')->truncate(); 

foreach ($tallas as $talla) {
    DB::table('tallas')->insert([
        'numero' => $talla, // O 'talla' según el nombre de tu columna
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

        // 4. Crear Administrador si no existe
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

        // 5. Crear Cliente de prueba si no existe
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
    }
}