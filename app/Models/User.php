<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     * Esto envía el campo 'role_type' automáticamente a Vue.
     *
     * @var list<string>
     */
    protected $appends = [
        'role_type',
    ];

    /**
     * Get the attributes that should be cast.
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

    /* |--------------------------------------------------------------------------
    | Relaciones de Perfil (Roles)
    |--------------------------------------------------------------------------
    | Conectamos el usuario con su tabla de perfil correspondiente.
    */

    public function propietario()
    {
        return $this->hasOne(Propietario::class, 'user_id', 'id');
    }

    public function vendedor()
    {
        return $this->hasOne(Vendedor::class, 'user_id', 'id');
    }

    public function cliente()
    {
        return $this->hasOne(Cliente::class, 'user_id', 'id');
    }

    /* |--------------------------------------------------------------------------
    | Atributos Calculados (Accessors)
    |--------------------------------------------------------------------------
    */

    /**
     * Determina el rol del usuario verificando en qué tabla tiene registro.
     * Retorna: 'propietario' | 'vendedor' | 'cliente' | 'user'
     */
    public function getRoleTypeAttribute(): string
    {
        // El orden importa: verificamos jerarquía
        if ($this->propietario()->exists()) {
            return 'propietario';
        }

        if ($this->vendedor()->exists()) {
            return 'vendedor';
        }

        if ($this->cliente()->exists()) {
            return 'cliente';
        }

        return 'user';
    }
}
