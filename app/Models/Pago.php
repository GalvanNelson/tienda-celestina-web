<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'codigo_pago';
    public $timestamps = true;

    protected $fillable = [
        'venta', 
        'cuota', // Puede ser null
        'fecha_pago', 
        'monto_pagado', 
        'metodo_pago'
    ];

    protected $casts = [
        'fecha_pago' => 'datetime',
        'monto_pagado' => 'decimal:2',
    ];

    public function detallePagoOnline()
    {
        return $this->hasOne(DetallePagoOnline::class, 'pago', 'codigo_pago');
    }
}
