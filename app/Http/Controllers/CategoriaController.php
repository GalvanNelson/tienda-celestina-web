<?php

namespace App\Http\Controllers;
use App\Models\Categoria;
use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;


class CategoriaController extends Controller
{
    public function index()
    {
        // Listamos categorías paginadas
        return Inertia::render('Propietario/Categorias/Index', [
            'categorias' => Categoria::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Propietario/Categorias/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:categorias,nombre',
        ]);

        Categoria::create(['nombre' => $request->nombre]);

        return redirect()->route('categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit($id)
    {
        $categoria = Categoria::findOrFail($id);
        
        return Inertia::render('Propietario/Categorias/Edit', [
            'categoria' => $categoria
        ]);
    }

    public function update(Request $request, $id)
    {
        $categoria = Categoria::findOrFail($id);

        $request->validate([
            // Validamos que el nombre sea único, ignorando el ID actual de esta categoría
            'nombre' => ['required', 'string', 'max:255', Rule::unique('categorias')->ignore($categoria->codigo_categoria, 'codigo_categoria')],
        ]);

        $categoria->update(['nombre' => $request->nombre]);

        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        if (Producto::where('categoria', $id)->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar: Hay productos asociados a esta categoría.');
        }

        $categoria = Categoria::findOrFail($id);
        $categoria->delete();

        return redirect()->back()->with('success', 'Categoría eliminada.');
    }
}
