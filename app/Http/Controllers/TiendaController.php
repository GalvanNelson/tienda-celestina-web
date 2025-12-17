<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TiendaController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        return Inertia::render('Cliente/Tienda/Catalogo', ['productos' => $productos]);
    }
}
