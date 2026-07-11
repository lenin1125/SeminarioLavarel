<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory; // <-- 1. Importamos la clase

class VentaDetalle extends Model
{
    use HasFactory; // <-- 2. Le damos el "superpoder" al modelo

    protected $table = 'ventas_detalle';

    protected $fillable = [
        'cantidad', 
        'precio_unitario', 
        'subtotal', 
        'zapato_id'
    ];

    public function zapato() {
        return $this->belongsTo(Zapato::class);
    }
}