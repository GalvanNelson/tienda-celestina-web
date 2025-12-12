<?php

namespace App\Http\Controllers;
use App\Models\UnidadMedida;
use App\Models\Producto;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class UnidadMedidaController extends Controller
{
    public function index()
    {
        return Inertia::render('Propietario/Unidades/Index', [
            'unidades' => UnidadMedida::latest()->paginate(10)
        ]);
    }

    public function create()
    {
        return Inertia::render('Propietario/Unidades/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255|unique:unidad_medidas,nombre',
        ]);

        UnidadMedida::create(['nombre' => $request->nombre]);

        return redirect()->route('unidades.index')->with('success', 'Unidad de medida creada.');
    }

    public function edit($id)
    {
        $unidad = UnidadMedida::findOrFail($id);
        
        return Inertia::render('Propietario/Unidades/Edit', [
            'unidad' => $unidad
        ]);
    }

    public function update(Request $request, $id)
    {
        $unidad = UnidadMedida::findOrFail($id);

        $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('unidad_medidas')->ignore($unidad->codigo_unidad_medida, 'codigo_unidad_medida')],
        ]);

        $unidad->update(['nombre' => $request->nombre]);

        return redirect()->route('unidades.index')->with('success', 'Unidad actualizada.');
    }

    public function destroy($id)
    {
        if (Producto::where('unidad_medida', $id)->exists()) {
            return redirect()->back()->with('error', 'No se puede eliminar: Hay productos usando esta unidad de medida.');
        }

        $unidad = UnidadMedida::findOrFail($id);
        $unidad->delete();

        return redirect()->back()->with('success', 'Unidad eliminada.');
    }
}
