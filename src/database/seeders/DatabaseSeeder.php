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
        // 1. Insertar Roles Base (Administrador = ID 1, Cliente = ID 2)
        DB::table('roles')->insert([
            ['id' => 1, 'nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Cliente', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Insertar Categorías Base para los Sneakers
        DB::table('categorias')->insert([
            ['nombre' => 'Deportivo', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Urbano', 'created_at' => now(), 'updated_at' => now()],
            ['nombre' => 'Casual', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Crear un Usuario Administrador de pruebas
        // Usamos el modelo User de Laravel apuntando al rol_id 1 (Administrador)
        User::create([
            'rol_id' => 1, // Administrador
            'nombre' => 'Admin',
            'apellido' => 'SneakersLH',
            'email' => 'admin@sneakerslh.com',
            'password' => Hash::make('admin12345'), // Contraseña encriptada de forma segura
            'telefono' => '3001234567',
        ]);

        // 4. Crear un Usuario Cliente de pruebas
        User::create([
            'rol_id' => 2, // Cliente
            'nombre' => 'Juan',
            'apellido' => 'Perez',
            'email' => 'juan@gmail.com',
            'password' => Hash::make('cliente12345'),
            'telefono' => '3159876543',
        ]);
    }
}