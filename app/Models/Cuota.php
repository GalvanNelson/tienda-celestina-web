<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuota extends Model
{
    protected $table = 'cuotas';
    protected $primaryKey = 'codigo_cuota';
    public $timestamps = true;

    protected $fillable = [
        'venta', 
        'numero_cuota', 
        'fecha_vencimiento', 
        'monto_programado', 
        'monto_abonado', 
        'estado_cuota'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'date',
        'monto_programado' => 'decimal:2',
        'monto_abonado' => 'decimal:2',
    ];

    public function ventaRelacion()
    {
        return $this->belongsTo(Venta::class, 'venta', 'codigo_venta');
    }
}
