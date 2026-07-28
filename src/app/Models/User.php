<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'is_admin',
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
     * Relación con el Modelo Rol
     */
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'rol_id');
    }

    /**
     * Encapsulación de lógica de negocio (Saber si es Admin)
     */
    public function isAdmin(): bool
    {
        return $this->email === 'admin@sneakerslh.com' 
            || (bool) ($this->is_admin ?? false)
            || ($this->rol_id === 1);
    }
}