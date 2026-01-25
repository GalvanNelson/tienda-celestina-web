<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
class TiendaController extends Controller
{
    public function index()
    {
        $productos = Producto::all();

        $productos->transform(function ($producto) {
            $producto->imagen_completa_url = $producto->imagen_url 
                ? asset('storage/' . $producto->imagen_url) 
                : null;
            return $producto;
        });

        return Inertia::render('Cliente/Tienda/Catalogo', ['productos' => $productos]);
    }
}
