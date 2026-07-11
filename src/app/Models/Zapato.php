<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zapato extends Model
{
    use HasFactory;

    // Bloque 2: Permisión de asignación masiva segura
    protected $fillable = ['nombre', 'descripcion', 'precio', 'activo', 'user_id'];

    // Relación inversa: Un zapato pertenece a un Usuario (Creador)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bloque 4: Scope para filtrar únicamente registros Activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Bloque 4: Scope para filtrar únicamente registros Inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('activo', false);
    }

    /**
     * Bloque 4: Scope para buscar coincidencias parciales por Nombre
     */
    public function scopeBuscar($query, $texto)
    {
        return $query->where('nombre', 'LIKE', "%{$texto}%");
    }
}