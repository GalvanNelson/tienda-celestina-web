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
        // Traemos productos con sus relaciones para mostrar nombres en vez de IDs
        $productos = Producto::with(['categoriaRelacion', 'unidadMedidaRelacion'])
            ->when($search, function ($query) use ($search) {
                $query->whereRaw("LOWER(nombre_producto) LIKE LOWER(?)", ["%{$search}%"]);
            })
            ->latest()
            ->paginate(10);

        $productos->getCollection()->transform(function ($producto) {
        // Genera la URL completa: http://tiendacelestina.tech/grupo14sa/.../storage/nombre.png
        $producto->imagen_url = $producto->imagen 
            ? asset('storage/' . $producto->imagen) 
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
        // Necesitamos estas listas para los <select> del formulario
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
            'categoria' => 'required|exists:categorias,codigo_categoria',
            'unidad_medida' => 'required|exists:unidad_medidas,codigo_unidad_medida',
            'imagen' => 'required|image|max:2048', // Max 2MB
        ]);

        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            // Guarda la imagen en la carpeta 'public/productos'
            $rutaImagen = $request->file('imagen')->store('/','public');
        }

        Producto::create([
            'nombre_producto' => $request->nombre_producto,
            'precio_unitario' => $request->precio_unitario,            
            'categoria' => $request->categoria,
            'unidad_medida' => $request->unidad_medida,
            'imagen' => $rutaImagen,
        ]);

        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {        
        $producto = Producto::with(['categoriaRelacion', 'unidadMedidaRelacion'])->findOrFail($id);

        // Genera la URL completa: http://tiendacelestina.tech/grupo14sa/.../storage/nombre.png
        $producto->imagen_url = $producto->imagen 
            ? asset('storage/' . $producto->imagen) 
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
        $producto = Producto::findOrFail($id);
        
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
            'stock' => 'required|numeric|min:0',
            'categoria' => 'required|exists:categorias,codigo_categoria',
            'unidad_medida' => 'required|exists:unidad_medidas,codigo_unidad_medida',
            'imagen' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('imagen');

        if ($request->hasFile('imagen')) {
            // Borrar imagen anterior si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete($producto->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('/','public');
        }

        $producto->update($data);

        return redirect()->route('productos.index')->with('success', 'Producto actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::findOrFail($id);
        
        if ($producto->imagen) {
            Storage::disk('public')->delete($producto->imagen);
        }
        
        $producto->delete();

        return redirect()->back()->with('success', 'Producto eliminado.');
    }
}
