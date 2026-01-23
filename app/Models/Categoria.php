<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'codigo_categoria';    
    public $timestamps = true;

    protected $fillable = ['nombre'];

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(
            Producto::class, 
            'producto_categoria', 
            'categoria', // FK en pivote para este modelo
            'producto'   // FK en pivote para el otro modelo
        );
    }
}
