<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePedido extends Model
{
    protected $table = 'detalle_pedido';

    protected $fillable = [
        'pedido_id',
        'producto_id',
        'cantidad',
        'talla',
        'precio_unitario',
    ];

    // Pertenece a un Pedido
    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id');
    }

    // Está asociado a un Producto
    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id');
    }
}