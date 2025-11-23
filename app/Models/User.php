<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dni',
        'role',
        'stand_id',
        'active',
        'failed_attempts',
        'locked_until',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'locked_until' => 'datetime',
        'password' => 'hashed',
        'active' => 'boolean',
    ];

    /**
     * Relación: Usuario pertenece a un Stand
     */
    public function stand()
    {
        return $this->belongsTo(Stand::class);
    }

    /**
     * Relación: Ventas registradas por el usuario
     */
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    /**
     * Scope: Usuarios activos
     */
    public function scopeActivos($query)
    {
        return $query->where('active', true);
    }

    /**
     * Scope: Usuarios por stand
     */
    public function scopePorStand($query, $standId)
    {
        return $query->where('stand_id', $standId);
    }

    /**
     * Verifica si el usuario está bloqueado
     */
    public function estaBloquedo()
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Obtiene el nombre del rol principal
     */
    public function getRolNombreAttribute()
    {
        return $this->roles->first()?->name ?? 'Sin Rol';
    }

    /**
     * Verifica si tiene acceso a un stand específico
     */
    public function tieneAccesoStand($standId)
    {
        // Admin tiene acceso a todo
        if ($this->hasRole('Administrador')) {
            return true;
        }

        // Contador también tiene acceso a todos los stands
        if ($this->hasRole('Contador')) {
            return true;
        }

        // Los demás solo a su stand asignado
        return $this->stand_id == $standId;
    }
}