<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetallePagoOnline extends Model
{
    protected $table = 'detalles_pago_online';
    protected $primaryKey = 'codigo_pago_online';
    public $timestamps = true;

    protected $fillable = [
        'pago', 
        'pedido_id', 
        'fecha_transaccion', 
        'hora_transaccion', 
        'metodo_pago_pasarela', 
        'estado_transaccion'
    ];
}
