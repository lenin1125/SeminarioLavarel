<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'usuario_id',
        'estado',
        'total',
    ];

    // Un pedido pertenece a un Usuario (Cliente)
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    // Un pedido tiene muchos detalles (productos asociados)
    public function detalles()
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    // Un pedido tiene un registro de Pago asociado
    public function pago()
    {
        return $this->hasOne(Pago::class, 'pedido_id');
    }

    // Un pedido tiene una Venta registrada
    public function venta()
    {
        return $this->hasOne(Venta::class, 'pedido_id');
    }
}