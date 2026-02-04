<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Vendedor;
use App\Models\Propietario;
use App\Models\Pago;
use App\Services\PagoFacilService;
use App\Models\DetallePagoOnline;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    public function index(){
        $ventas = Venta::with(['clienteRelacion'])->orderBy('codigo_venta', 'desc')->get();

        return Inertia::render('Vendedor/Ventas/Index', [
            'ventas' => $ventas
        ]);
    }
    // Vista para crear nueva venta (El Punto de Venta)
    public function create(){
        $productos = Producto::where('estado_producto', 'activo')->get();
        $clientes = Cliente::where('estado_cliente', 'activo')->get();

        return Inertia::render('Vendedor/Ventas/Create', [
            'productos' => $productos,
            'clientes' => $clientes
        ]);       
    }

    public function show($id)
    {        
        $venta = Venta::with(['clienteRelacion', 'detalles.productoRelacion'])->findOrFail($id);
        $cliente = $venta->clienteRelacion;

        return Inertia::render('Vendedor/Ventas/Show', [
            'venta' => $venta,
            'cliente' => $cliente
        ]);
    }

    public function update(Request $request, $id)
    {        
        $venta = Venta::findOrFail($id);
        
        // Solo permitir actualización si el estado es 'pendiente'
        if ($venta->estado_venta !== 'pendiente') {
            return redirect()->back()->with('error', 'Solo se pueden actualizar ventas en estado pendiente.');
        }
        
        $request->validate([
            'estado_venta' => 'required|in:pendiente,pagado,enviado,entregado,cancelado',
        ]);
        
        $venta->update([
            'estado_venta' => $request->estado_venta
        ]);
        
        return redirect()->route('ventas.show', $id)->with('success', 'Estado de venta actualizado correctamente.');
    }

    // Procesar la Venta
    public function store(Request $request, PagoFacilService $pagoFacilService)
    {                
        $this->callbackUrl = env('PAGOFACIL_CALLBACK_URL');
        $id_responsable = $this->obtenerIdResponsable();       
            
        $request->validate([
            'cliente_id' => 'required|exists:clientes,codigo_cliente',
            'carrito' => 'required|array|min:1',
            'carrito.*.codigo_producto' => 'required|exists:productos,codigo_producto',
            'carrito.*.cantidad' => 'required|numeric|min:1',
            'total' => 'required|numeric',
            'tipo_pago' => 'required|in:contado,credito',
            // Validamos el metodo_pago solo si es al contado
            'metodo_pago' => 'required_if:tipo_pago,contado|in:efectivo,qr',
        ]);
        
        try {
            DB::beginTransaction();

            $cliente = Cliente::findOrFail($request->cliente_id);   

            // 1. Estado inicial
            // Si es Crédito -> Pendiente
            // Si es Contado (Efectivo) -> Pagado
            // Si es Contado (QR) -> Pendiente (hasta que pague el QR)
            $estado_venta = 'pendiente';

            if ($request->tipo_pago === 'contado' && $request->metodo_pago === 'efectivo') {
                $estado_venta = 'pagado';
            }

            // 2. Crear Cabecera de Venta
            $venta = Venta::create([
                'cliente' => $cliente->codigo_cliente,
                'vendedor' => $id_responsable,
                'fecha_venta' => now(),
                'monto_total' => $request->total,
                'tipo_pago' => $request->tipo_pago, 
                'estado_venta' => $estado_venta,
                'interes_aplicado' => 0,
            ]);                        
            
            // 3. Procesar Detalles y Stock
            $detallesAPI = []; 
            $serial = 1;

            foreach ($request->carrito as $item) {
                $producto = Producto::lockForUpdate()->find($item['codigo_producto']);
                
                // Validar stock aquí si lo deseas
                if ($producto->stock < $item['cantidad']) {
                    throw new \Exception("Stock insuficiente para: " . $producto->nombre_producto);
                }

                DetalleVenta::create([
                    'venta' => $venta->codigo_venta,
                    'producto' => $producto->codigo_producto,
                    'precio_unitario' => $producto->precio_unitario,
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $item['cantidad'] * $producto->precio_unitario,                    
                ]);
                
                // Descontar Stock
                $producto->decrement('stock', $item['cantidad']);

                // Preparar array para PagoFácil (API espera integers o floats)
                $detallesAPI[] = [
                    "serial" => $serial++,
                    "product" => $producto->nombre_producto,
                    "quantity" => (int)$item['cantidad'],
                    "price" => (float)$producto->precio_unitario,
                    "discount" => 0,
                    "total" => (float)($item['cantidad'] * $producto->precio_unitario)
                ];
            }    
            
            $qrImage = null;

            // 4. LÓGICA DE PAGOS
            
            // CASO A: CRÉDITO
            if ($request->tipo_pago === 'credito') {
                if (($cliente->saldo_actual + $request->total) > $cliente->limite_credito) {
                    throw new \Exception("El cliente excede su límite de crédito.");
                }
                $cliente->increment('saldo_actual', $request->total);
            }
            
            // CASO B: CONTADO (Decidir entre Efectivo o QR)
            elseif ($request->tipo_pago === 'contado') {
                
                if ($request->metodo_pago === 'efectivo') {
                    // Pago inmediato
                    Pago::create([
                        'venta' => $venta->codigo_venta,
                        'fecha_pago' => now(),
                        'monto_pagado' => $request->total,
                        'metodo_pago' => 'efectivo'
                    ]);
                } 
                elseif ($request->metodo_pago === 'qr') {
                    // Generar QR y dejar pago como pendiente
                    
                    // 1. Crear Pago (monto 0 o total, según prefieras para reportes. Usualmente 0 hasta confirmar)
                    $pago = Pago::create([
                        'venta' => $venta->codigo_venta,
                        'fecha_pago' => now(), 
                        'monto_pagado' => 0,   // Pendiente de pago real
                        'metodo_pago' => 'qr'
                    ]);

                    // 2. ID Único transacción (Prefijo + VentaID + Random)
                    $transaccionId = "GRUPO14SA_" . $venta->codigo_venta;

                    // 3. Preparar datos para el Servicio
                    // OJO: El servicio espera 'cliente_id' como string en clientCode
                    $datosQR = [
                        'cliente_nombre'   => $cliente->nombre_cliente,
                        'cliente_ci'       => '11325240',      // <--- FIJO SEGÚN SOLICITUD
                        'cliente_telefono' => '69008952',      // <--- FIJO SEGÚN SOLICITUD
                        'cliente_email'    => 'prueba@gmail.com', // Puedes usar fijo o el del cliente
                        'cliente_id'       => "CLIENTEGRUP014SA_" . $cliente->codigo_cliente, // Ajustado al estilo del postman
                        'id_transaccion'   => $transaccionId,  // GRUPO14SA_...
                        'monto'            => 0.01,            // <--- FIJO 0.01
                        'callback_url'     => $this->callbackUrl,
                        'detalles'         => $detallesAPI     // Array de productos
                    ];

                    // 4. Llamar al Servicio
                    try {
                        $respuestaAPI = $pagoFacilService->generarQR($datosQR);
                        
                        // Capturar la imagen QR (Base64)
                        $qrImage = null;

                        // Caso 1: La imagen viene directa como string (Versiones antiguas)
                        if (isset($respuestaAPI['values']) && is_string($respuestaAPI['values'])) {
                            $qrImage = $respuestaAPI['values'];
                        }
                        // Caso 2: Viene como 'qrImage' (Según documentación antigua)
                        elseif (isset($respuestaAPI['values']['qrImage'])) {
                            $qrImage = $respuestaAPI['values']['qrImage'];
                        }
                        // Caso 3: Viene como 'qrBase64' (TU CASO ACTUAL - Status 2007)
                        elseif (isset($respuestaAPI['values']['qrBase64'])) {
                            $qrImage = $respuestaAPI['values']['qrBase64'];
                        }

                        // Validación final
                        if (!$qrImage) {
                            throw new \Exception("La API no devolvió la imagen QR. Respuesta: " . json_encode($respuestaAPI));
                        }

                        // Guardar Detalle Online
                        // También puedes guardar el transactionId real que te devolvió la API
                        $idRealPasarela = $respuestaAPI['values']['transactionId'] ?? $transaccionId;

                        DetallePagoOnline::create([
                            'pago' => $pago->codigo_pago,
                            'pedido_id' => $transaccionId, // Tu ID interno GRUPO14SA_...
                            'fecha_transaccion' => now(),
                            'hora_transaccion' => now(),
                            'metodo_pago_pasarela' => 'pagofacil_qr',
                            'estado_transaccion' => 'PENDIENTE'
                        ]);

                    } catch (\Exception $e) {
                        throw $e; 
                    }
                }
            }
            
            DB::commit();

            // 5. REDIRECCIONES
            
            // Si hay imagen QR, mandamos a la vista de escaneo
            if ($qrImage) {
                return Inertia::render('Ventas/ShowQR', [
                    'qrImage' => $qrImage,
                    'ventaId' => $venta->codigo_venta,
                    'total' => (float)$request->total
                ]);
            }

            $mensaje = $request->tipo_pago === 'credito' 
                ? 'Venta a CRÉDITO registrada.' 
                : 'Venta al CONTADO registrada exitosamente.';

            return redirect()->route('ventas.create')->with('success', $mensaje);

        } catch (\Exception $e) {
            DB::rollBack();
            // Loguear el error real para debugging
            // \Log::error($e->getMessage()); 
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
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
