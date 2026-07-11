<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ZapatoFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Genera una palabra aleatoria para el nombre
            'nombre' => $this->faker->word() . ' Deportivo', 
            // Genera una oración para la descripción
            'descripcion' => $this->faker->sentence(),
            // Genera un precio aleatorio entre 50,000 y 300,000
            'precio' => $this->faker->randomFloat(2, 50000, 300000),
            // 80% de probabilidad de ser true (activo)
            'activo' => $this->faker->boolean(80),
            // Crea un usuario automáticamente si no se le pasa uno
            'user_id' => User::factory(), 
        ];
    }
}