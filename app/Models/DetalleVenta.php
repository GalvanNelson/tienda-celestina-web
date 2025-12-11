<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';
    protected $primaryKey = 'codigo_detalle_venta';
    public $timestamps = true; // Detalles usualmente no llevan timestamps

    protected $fillable = [
        'venta', 
        'producto', 
        'precio_unitario', 
        'cantidad', 
        'subtotal'
    ];

    public function ventaRelacion()
    {
        return $this->belongsTo(Venta::class, 'venta', 'codigo_venta');
    }

    public function productoRelacion()
    {
        return $this->belongsTo(Producto::class, 'producto', 'codigo_producto');
    }
}
