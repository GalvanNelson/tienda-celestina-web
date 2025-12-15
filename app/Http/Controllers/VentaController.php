<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Vendedor;
use App\Models\Propietario;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    // Vista para crear nueva venta (El Punto de Venta)
    public function create()
    {
        return Inertia::render('Ventas/Create', [        
            'clientes' => Cliente::all(),
            // Enviamos productos con stock positivo
            'productos' => Producto::where('stock', '>', 0)
                ->with('categoriaRelacion') // Opcional: Para mostrar la categoría
                ->orderBy('nombre_producto')
                ->get() 
        ]);        
    }

    // Procesar la Venta
    public function store(Request $request)
    {                
        $id_responsable = $this->obtenerIdResponsable();
            
        $request->validate([
            'cliente_id' => 'required|exists:clientes,codigo_cliente',
            'carrito' => 'required|array|min:1',
            'carrito.*.codigo_producto' => 'required|exists:productos,codigo_producto',
            'carrito.*.cantidad' => 'required|numeric|min:1',
            'total' => 'required|numeric',
            // NUEVO: Validar que envíen el tipo de pago correcto
            'tipo_pago' => 'required|in:contado,credito', 
        ]);
        
        try {
            DB::beginTransaction();

            $cliente = Cliente::findOrFail($request->cliente_id);            
                            
            // 1. Crear Cabecera de Venta
            $venta = Venta::create([
                'cliente' => $cliente->codigo_cliente,
                'vendedor' => $id_responsable,
                'fecha_venta' => now(),
                'monto_total' => $request->total,
                'tipo_pago' => $request->tipo_pago, // Guardamos si fue contado o crédito
                'estado_venta' => 'finalizada',
                'interes_aplicado' => 0,
            ]);                        
            
            // 2. Procesar cada producto (Igual que antes)
            foreach ($request->carrito as $item) {
                $producto = Producto::lockForUpdate()->find($item['codigo_producto']);

                // if ($producto->stock < $item['cantidad']) {
                //     throw new \Exception("Stock insuficiente para: " . $producto->nombre_producto);
                // }

                DetalleVenta::create([
                    'venta' => $venta->codigo_venta,
                    'producto' => $producto->codigo_producto,
                    'precio_unitario' => $producto->precio_unitario,
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $item['cantidad'] * $producto->precio_unitario,                    
                ]);

                //$producto->decrement('stock', $item['cantidad']);
            }               
            // Confirmar transacción            
            DB::commit();

            $mensaje = $request->tipo_pago === 'credito' 
                ? 'Venta a CRÉDITO registrada. Saldo actualizado.' 
                : 'Venta al CONTADO registrada exitosamente.';

            return redirect()->route('ventas.create')->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al procesar venta: ' . $e->getMessage());
        }
    }

    // Obtener el ID del responsable según el rol del usuario autenticado
    private function obtenerIdResponsable()
    {
        $user = Auth::user();
        $id_responsable = null;

        if ($user->role_type === 'vendedor') {
            $perfil = Vendedor::where('user_id', $user->id)->first();
            $id_responsable = $perfil ? $perfil->codigo_vendedor : null;
        } 
        elseif ($user->role_type === 'propietario') {
            $perfil = Propietario::where('user_id', $user->id)->first();
            $id_responsable = $perfil ? $perfil->codigo_propietario : null;
        }
        elseif ($user->role_type === 'cliente') {            
            $perfil = Cliente::where('user_id', $user->id)->first();
            $id_responsable = $perfil ? $perfil->codigo_cliente : null;
        }

        return $id_responsable;
    }
}
