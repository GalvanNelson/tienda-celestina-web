<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = 'clientes';
    protected $primaryKey = 'codigo_cliente';    
    public $timestamps = true;

    protected $fillable = [
        'user_id', 
        'nombre_cliente', 
        'carnet_identidad', 
        'limite_credito', 
        'saldo_actual'
    ];

    protected $casts = [
        'limite_credito' => 'decimal:2',
        'saldo_actual' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
