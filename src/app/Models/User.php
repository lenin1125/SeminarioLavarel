<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 👈 1. Importado Sanctum

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; // 👈 1. Habilitado Sanctum

    /**
     * Los atributos que son asignables de forma masiva.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',   
        'apellido',
        'email',
        'password',
        'rol_id',
    ];

    /**
     * Los atributos que deben ocultarse para la serialización.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Atributos que deben ser convertidos.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relación con el Modelo Rol (Soluciona el error 500 en la API)
     */
    public function rol() // 👈 2. Relación agregada
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }
}