<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductoImagen extends Model
{
    use HasFactory;

    // Cambiamos al nombre exacto en singular
    protected $table = 'producto_imagen'; 

    protected $fillable = [
        'producto_id',
        'imagen_url',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}