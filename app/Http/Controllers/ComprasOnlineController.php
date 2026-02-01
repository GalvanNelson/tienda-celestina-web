<?php

namespace App\Http\Controllers;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\DetallePagoOnline;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ComprasOnlineController extends Controller
{
    public function index()
    {                 
        $codigoCliente = Auth::user()->cliente->codigo_cliente;
        
        $compraPorCliente = Venta::with(['clienteRelacion']) // Cargamos el vendedor para saber quién lo atendió
            ->where('cliente', $codigoCliente) // Filtro por el ID del cliente autenticado
            ->orderBy('fecha_venta', 'desc')   // Orden de la más reciente a la más antigua
            ->paginate(10);
        
        // 3. Retornamos a la vista de Inertia
        return Inertia::render('Cliente/ComprasOnline/Index', [
            'compras' => $compraPorCliente
        ]);
    }

    public function show($id)
    {                
        $compra = Venta::with(['detalles.productoRelacion', 'vendedorRelacion'])
            ->findOrFail($id);
        
        if ($compra->cliente !== Auth::user()->cliente->codigo_cliente) {
            abort(403);
        }

        return Inertia::render('Cliente/ComprasOnline/Show', [
            'compra' => $compra
        ]);
    }

    public function store(Request $request)
    {                   
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,qr',
            'carrito' => 'required|array',
            'total' => 'required|numeric',
            'tipo_pago' => 'required|in:contado, credito'
        ]);
        
        if ($request->tipo_pago === 'contado') {            
            $this->crearVentaPendiente($request);
            return redirect()->route('cliente.tienda')->with('success', 'Venta pendiente creada exitosamente.');
        }
    }

    public function checkout()
    {
        return Inertia::render('Cliente/ComprasOnline/Checkout');
    }

    public function crearVentaPendiente ($request){
        DB::beginTransaction();
        try {
            $venta = Venta::create([
                'cliente' => Auth::user()->cliente->codigo_cliente, // Asumiendo relación                
                'fecha_venta' => now(),
                'monto_total' => $request->total,
                'tipo_pago' => 'contado', 
                'estado_venta' => 'pendiente', // Importante
                'interes_aplicado' => 0
            ]);

            foreach ($request->carrito as $item) {
                DetalleVenta::create([
                    'venta' => $venta->codigo_venta,
                    'producto' => $item['id'],
                    'precio_unitario' => $item['precio'],
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $item['precio'] * $item['cantidad']
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al registrar venta pendiente: ' . $e->getMessage());
            throw $e; // Re-lanzar la excepción para manejarla en el nivel superior
        }

    }
}
