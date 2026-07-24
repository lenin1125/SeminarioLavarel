<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'genero', // Agregado al fillable
        'imagen_url', 
        'categoria_id',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id');
    }

    public function imagenes()
{
    return $this->hasMany('App\Models\ProductoImagen'); 
    // Si tu archivo se llama Producto_Imagen.php, cámbialo por: 'App\Models\Producto_Imagen'
}

public function tallas()
{
    return $this->belongsToMany(Talla::class, 'producto_talla')
                ->withPivot('cantidad');
}
}