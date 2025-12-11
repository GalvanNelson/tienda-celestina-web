<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnidadMedida extends Model
{
    protected $table = 'unidad_medidas';
    protected $primaryKey = 'codigo_unidad_medida';    
    public $timestamps = true;

    protected $fillable = ['nombre'];
}
