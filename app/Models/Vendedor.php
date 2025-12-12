<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    protected $table = 'vendedores';
    protected $primaryKey = 'codigo_vendedor';
    public $timestamps = true;

    protected $fillable = [
        'user_id', 
        'carnet_identidad', 
        'telefono', 
        'sueldo_basico', 
        'porcentaje_comision', 
        'fecha_contratacion', 
        'estado'
    ];

    protected $casts = [
        'sueldo_basico' => 'decimal:2',
        'porcentaje_comision' => 'decimal:2',
        'fecha_contratacion' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
