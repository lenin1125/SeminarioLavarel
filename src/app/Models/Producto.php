<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'genero',
        'imagen_url',
        'categoria_id',
        'activo',
    ];

    /**
     * Relación con Categoria (Un zapato pertenece a una categoría)
     */
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    /**
     * Relación con Tallas (Muchos a Muchos con la tabla pivote producto_talla)
     */
    public function tallas()
    {
        return $this->belongsToMany(Talla::class, 'producto_talla')
                    ->withPivot(['stock', 'cantidad']);
    }
}