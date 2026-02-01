<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'codigo_venta';
    public $timestamps = true; // Este sí tiene created_at

    protected $fillable = [
        'cliente', 
        'vendedor', 
        'fecha_venta', 
        'monto_total', 
        'tipo_pago', 
        'metodo_pago',
        'estado_venta', 
        'interes_aplicado'
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'monto_total' => 'decimal:2',
        'interes_aplicado' => 'decimal:2',
    ];

    // Relaciones
    public function clienteRelacion()
    {
        return $this->belongsTo(Cliente::class, 'cliente', 'codigo_cliente');
    }

    public function vendedorRelacion()
    {
        return $this->belongsTo(Vendedor::class, 'vendedor', 'codigo_vendedor');
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class, 'venta', 'codigo_venta');
    }

    public function cuotas()
    {
        return $this->hasMany(Cuota::class, 'venta', 'codigo_venta');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'venta', 'codigo_venta');
    }
}
