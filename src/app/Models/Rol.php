<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    // Forzamos el nombre de la tabla ya que por defecto Laravel busca "rols" en inglés
    protected $table = 'roles'; 

    protected $fillable = ['nombre'];

    // Un Rol tiene muchos Usuarios
    public function usuarios()
    {
        return $this->hasMany(User::class, 'rol_id');
    }
}