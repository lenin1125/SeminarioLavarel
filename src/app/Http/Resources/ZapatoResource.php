<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZapatoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nombre_comercial' => $this->nombre,
            'descripcion_breve' => $this->descripcion ?? 'Sin descripción disponible',
            'precio_venta' => (float) $this->precio,
            'estado_disponibilidad' => $this->activo ? 'Disponible en inventario' : 'Agotado / Inactivo',
            'fecha_registro' => $this->created_at->toIso8601String(),
        ];
    }
}
