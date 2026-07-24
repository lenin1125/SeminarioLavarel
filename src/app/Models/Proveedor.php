<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = ['nombre', 'contacto', 'telefono'];

    // Un proveedor trabaja con muchos productos (Muchos a Muchos)
    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'producto_proveedor');
    }
}