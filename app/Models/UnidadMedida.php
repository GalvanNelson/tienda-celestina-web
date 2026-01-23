<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidad_medidas';
    protected $primaryKey = 'codigo_unidad_medida';    
    public $timestamps = true;

    protected $fillable = ['nombre'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class, 'unidad_medida', 'codigo_unidad_medida');
    }
}
