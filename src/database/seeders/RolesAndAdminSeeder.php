<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RolesAndAdminSeeder extends Seeder
{
    public function run()
    {
        // 1. Crear Roles si no existen
        DB::table('roles')->insertOrIgnore([
            ['id' => 1, 'nombre' => 'Administrador', 'created_at' => now(), 'updated_at' => now()],
            ['id' => 2, 'nombre' => 'Cliente', 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 2. Crear el Usuario Administrador por defecto
        User::updateOrCreate(
            ['email' => 'admin@sneakerslh.com'], // Busca por este email
            [
                'rol_id'   => 1,
                'nombre'   => 'Admin',
                'apellido' => 'SneakersLH',
                'password' => Hash::make('admin12345'), // Contraseña encriptada de forma segura
                'telefono' => '3001234567',
            ]
        );
    }
}