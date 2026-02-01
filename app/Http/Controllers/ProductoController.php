<?php

namespace App\Http\Controllers;
use App\Models\Producto;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Inertia\Inertia; 
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $search = request()->query('search');
        
        // Cargamos 'unidadMedida' (singular) porque ahora es BelongsTo
        $productos = Producto::with(['categorias', 'unidadMedida'])
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("LOWER(nombre_producto) LIKE LOWER(?)", ["%{$search}%"]);
            })
            ->latest()
            ->paginate(10);

        $productos->getCollection()->transform(function ($producto) {
            $producto->imagen_completa_url = $producto->imagen_url 
                ? asset('storage/' . $producto->imagen_url) 
                : null;
            return $producto;
        });

        return Inertia::render('Propietario/Productos/Index', [
            'productos' => $productos,
            'filters' => request()->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Propietario/Productos/Create', [
            'categorias' => Categoria::all(),
            'unidades' => UnidadMedida::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'precio_unitario' => 'required|numeric|min:0',            
            'grupo'           => 'required|integer', // Campo requerido en migración
            'categorias'      => 'required|array',   // Array de IDs para la tabla pivot
            'unidad_medida'   => 'required|integer', // ID único de unidad de medida
            'imagen_url'      => 'nullable|image|max:2048',
        ]);
        dd($request->all());
       // dd($request->all());

        $nombreImagen = null;
        if ($request->hasFile('imagen_url')) {
            $nombreImagen = $request->file('imagen_url')->store('/', 'public');
        }

        $producto = Producto::create([
            'nombre_producto' => $request->nombre_producto,
            'precio_unitario' => $request->precio_unitario,            
            'grupo'           => $request->grupo,
            'unidad_medida' => $request->unidad_medida,
            'imagen_url'      => $nombreImagen,
        ]);

        $producto->categorias()->attach($request->categorias);
        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');    
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {        
        $producto = Producto::with(['unidadMedida'])->findOrFail($id);

        $producto->imagen_completa_url = $producto->imagen_url 
            ? asset('storage/' . $producto->imagen_url) 
            : null;
        
        return Inertia::render('Propietario/Productos/Show', [
            'producto' => $producto,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Cargar singular 'unidadMedida'
        $producto = Producto::with(['categorias'])->findOrFail($id);        
        // Agregar imagen completa URL
        $producto->imagen_completa_url = $producto->imagen_url 
            ? asset('storage/' . $producto->imagen_url) 
            : null;
        
        return Inertia::render('Propietario/Productos/Edit', [
            'producto' => $producto,
            'categorias' => Categoria::all(),
            'unidades' => UnidadMedida::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'nombre_producto' => 'required|string|max:255',
            'precio_unitario' => 'required|numeric|min:0',
            'grupo'           => 'required|integer',
            'categorias'      => 'required|array',
            'unidad_medida'   => 'required|integer',
            'imagen_url'      => 'nullable|image|max:2048',
        ]);

        // Incluimos 'unidad_medida' en los datos a actualizar
        $data = $request->only(['nombre_producto', 'precio_unitario', 'grupo', 'unidad_medida']);

        if ($request->hasFile('imagen_url')) {
            if ($producto->imagen_url) {
                Storage::disk('public')->delete($producto->imagen_url);
            }
            $data['imagen_url'] = $request->file('imagen_url')->store('/', 'public');
        }

        $producto->update($data);

        // Solo sincronizamos categorías
        $producto->categorias()->sync($request->categorias);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        
        // Desasociar las categorías antes de eliminar
        $producto->categorias()->detach();
        
        if ($producto->imagen_url) {
            Storage::disk('public')->delete($producto->imagen_url);
        }
        
        $producto->delete();

        return redirect()->back()->with('success', 'Producto eliminado.');
    }
}
