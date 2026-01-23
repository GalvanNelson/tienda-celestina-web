<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{
    protected $table = 'productos';
    protected $primaryKey = 'codigo_producto';
    public $timestamps = true;

    protected $fillable = [
        'nombre_producto', 
        'imagen_url', 
        'precio_unitario',                 
        'grupo',
        'unidad_medida',
    ];

    protected $casts = [
        'precio_unitario' => 'decimal:2',
        'grupo' => 'integer',
        'unidad_medida' => 'integer',
    ];

    public function categorias(): BelongsToMany
    {
        return $this->belongsToMany(Categoria::class, 'producto_categoria', 'producto', 'categoria');
    }
    
    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_medida', 'codigo_unidad_medida');
    }
}