<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'codigo_producto';
    public $timestamps = true;

    protected $fillable = [
        'nombre_producto', 
        'imagen', 
        'precio_unitario', 
        'stock', 
        'categoria', 
        'unidad_medida'
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'stock' => 'decimal:2',
    ];

    // Relaciones
    public function categoriaRelacion()
    {
        return $this->belongsTo(Categoria::class, 'categoria', 'codigo_categoria');
    }

    public function unidadMedidaRelacion()
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida', 'codigo_unidad_medida');
    }
}
