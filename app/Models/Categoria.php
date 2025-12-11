<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    protected $table = 'categorias';
    protected $primaryKey = 'codigo_categoria';    
    public $timestamps = true;

    protected $fillable = ['nombre'];
}
