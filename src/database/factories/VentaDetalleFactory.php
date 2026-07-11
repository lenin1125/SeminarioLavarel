<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Zapato;

class VentaDetalleFactory extends Factory
{
    public function definition(): array
    {
        $cantidad = $this->faker->numberBetween(1, 5);
        $precio_unitario = $this->faker->randomFloat(2, 50000, 300000);

        return [
            'cantidad' => $cantidad,
            'precio_unitario' => $precio_unitario,
            'subtotal' => $cantidad * $precio_unitario,
            // Aquí cumplimos el requisito: crea un zapato automáticamente
            'zapato_id' => Zapato::factory(), 
        ];
    }
}