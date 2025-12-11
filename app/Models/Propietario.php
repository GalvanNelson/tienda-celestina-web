<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Propietario extends Model
{
    protected $table = 'propietarios';
    protected $primaryKey = 'codigo_propietario';
    public $timestamps = true;

    protected $fillable = [
        'user_id', 
        'razon_social', 
        'nit_o_ruc', 
        'direccion_oficina', 
        'telefono_contacto'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
