<?php

namespace App\Http\Controllers;
use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Pago;
use App\Models\DetallePagoOnline;
use App\Services\PagoFacilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VentaOnlineController extends Controller
{
    public function store(Request $request, PagoFacilService $pagoFacilService)
    {        
        $this->callbackUrl = env('PAGOFACIL_CALLBACK_URL');
        
        // 1. Validar carrito y datos (simplificado)
        $request->validate([
            'metodo_pago' => 'required|in:efectivo,qr',
            'carrito' => 'required|array', // Asumiendo que envías el carrito desde Vue
            'total' => 'required|numeric'
        ]);

        try {
            DB::beginTransaction();

            // 2. Crear la Venta (Estado Pendiente)
            $venta = Venta::create([
                'cliente' => Auth::user()->cliente->codigo_cliente, // Asumiendo relación
                'vendedor' => 1, // O un vendedor por defecto para web
                'fecha_venta' => now(),
                'monto_total' => $request->total,
                'tipo_pago' => 'contado', 
                'estado_venta' => 'pendiente', // Importante
                'interes_aplicado' => 0
            ]);
            
            // 3. Guardar Detalles (Iterar carrito)
            $detallesAPI = []; 
            $serial = 1;
            foreach ($request->carrito as $item) {
                DetalleVenta::create([
                    'venta' => $venta->codigo_venta,
                    'producto' => $item['id'],
                    'precio_unitario' => $item['precio'],
                    'cantidad' => $item['cantidad'],
                    'subtotal' => $item['precio'] * $item['cantidad']
                ]);
                $detallesAPI[] = [
                    'serial' => $serial++,
                    'product' => $item['nombre'],
                    'quantity' => $item['cantidad'],
                    'price' => $item['precio'],
                    'discount' => 0,
                    'total' => $item['precio'] * $item['cantidad']
                ];
            }
            
            // 4. Lógica según método de pago
            if ($request->metodo_pago === 'efectivo') {
                DB::commit();
                // Redirigir a página de éxito simple
                return to_route('venta.exito', ['id' => $venta->codigo_venta]);
            } 
            
            // CASO QR
            elseif ($request->metodo_pago === 'qr') {
                
                // Generar ID único para la pasarela (Mismo formato que espera tu Callback)
                $pedidoId = 'GRUPO14SA_PRUEBA_' . $venta->codigo_venta;

                // Crear registro de Pago (incompleto aun)
                $pago = Pago::create([
                    'venta' => $venta->codigo_venta,
                    'fecha_pago' => now(), // Ojo, esto se confirmará luego
                    'monto_pagado' => $request->total,
                    'metodo_pago' => 'QR - PagoFacil'
                ]);
                
                // Crear registro Detalle Pago Online para trackear
                DetallePagoOnline::create([
                   'pago' => $pago->codigo_pago,
                   'pedido_id' => $pedidoId,
                   'fecha_transaccion' => now(),
                   'hora_transaccion' => now(),
                   'metodo_pago_pasarela' => 'QR',
                   'estado_transaccion' => '1' // Pendiente
                ]);
                
                // Preparar datos para el Servicio (según tu PagoFacilService)
                $datosQR = [
                    'cliente_nombre' => Auth::user()->name,
                    'cliente_ci' => '11325240', // Hardcode para pruebas
                    'cliente_telefono' => '69008952', // Hardcode para pruebas
                    'cliente_email' => 'prueba@gmail.com', // Hardcode para pruebas
                    'cliente_id' => Auth::user()->cliente->codigo_cliente,
                    'id_transaccion' => $pedidoId,
                    'monto' => 0.01, // Hardcode para pruebas
                    'callback_url' => $this->callbackUrl, // Importante
                    'detalles' => [
                        // PagoFacil pide array de detalles
                        ...$detallesAPI
                    ] 
                ];                
                // Llamar al servicio
                $respuestaAPI = $pagoFacilService->generarQR($datosQR);
                
                // Extraer la imagen base64 del QR de la respuesta (prioridad qrBase64 según tu ejemplo)
                $qrImage = data_get($respuestaAPI, 'values.qrBase64')
                    ?? data_get($respuestaAPI, 'values.qrImage')
                    ?? data_get($respuestaAPI, 'values.image')
                    ?? data_get($respuestaAPI, 'values.base64')
                    ?? data_get($respuestaAPI, 'qrBase64')
                    ?? data_get($respuestaAPI, 'qrImage')
                    ?? data_get($respuestaAPI, 'image')
                    ?? null;

                if (!$qrImage) {
                    Log::error('PagoFacil sin qrImage', ['respuesta' => $respuestaAPI]);
                    throw new \Exception('No se recibió la imagen QR de PagoFácil. Revisa logs para ver la respuesta completa.');
                }

                DB::commit();

                // Retornar a una vista de Inertia "EsperandoPago" con el QR
                return Inertia::render('Cliente/Checkout/PagoQR', [ //
                    'venta_id' => $venta->codigo_venta,
                    'qr_image' => $qrImage,
                    'monto' => $request->total
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar venta: ' . $e->getMessage()]);
        }
    }

    // Método para que el Frontend consulte el estado (Polling)
    public function checkStatus($id)
    {
        $venta = Venta::find($id);
        
        if ($venta->estado_venta === 'pagado') {
            return response()->json(['status' => 'pagado']);
        }

        return response()->json(['status' => 'pendiente']);
    }

    // Callback público de PagoFácil
    public function callback(Request $request)
    {
        // Logueamos para depurar (puedes quitarlo en producción)
        Log::info('PagoFácil callback recibido', ['payload' => $request->all()]);

        // Extraer el identificador del pedido enviado cuando generamos el QR
        $paymentNumber = $request->input('paymentNumber')
            ?? $request->input('values.paymentNumber')
            ?? $request->input('values.payment_number')
            ?? $request->input('orderId')
            ?? $request->input('values.orderId');

        if (!$paymentNumber) {
            return response()->json(['message' => 'paymentNumber no encontrado'], 400);
        }

        // Buscar el detalle online por pedido_id
        $detalle = DetallePagoOnline::where('pedido_id', $paymentNumber)->first();
        if (!$detalle) {
            return response()->json(['message' => 'Pedido no encontrado'], 404);
        }

        // Determinar estado desde el callback
        $estadoPasarela = $request->input('paymentStatus')
            ?? $request->input('values.paymentStatus')
            ?? $request->input('status')
            ?? $request->input('values.status')
            ?? $request->input('state');

        // Casos soportados: numérico 2 (pagado), string "2", o palabras approved/paid/success
        $estadoNormalizado = is_string($estadoPasarela) ? strtolower($estadoPasarela) : $estadoPasarela;
        $codigo = is_numeric($estadoNormalizado) ? (int) $estadoNormalizado : null;
        $pagado = ($codigo === 2)
            || (is_string($estadoNormalizado) && (
                str_contains($estadoNormalizado, 'approved')
                || str_contains($estadoNormalizado, 'paid')
                || str_contains($estadoNormalizado, 'success')
                || $estadoNormalizado === '2'
                || $estadoNormalizado === 'aprobado'
            ));

        DB::beginTransaction();
        try {
            // Actualizar detalle online (guardar tal cual llega para trazabilidad)
            $detalle->estado_transaccion = $estadoPasarela ?? ($pagado ? '2' : 'rechazado');
            $detalle->save();

            // Marcar venta/pago si está aprobado
            $pago = Pago::find($detalle->pago);
            if ($pago) {
                if ($pagado) {
                    $pago->fecha_pago = now();
                }
                $pago->save();
            }

            if ($pago) {
                $venta = Venta::find($pago->venta);
                if ($venta && $pagado) {
                    $venta->estado_venta = 'pagado';
                    $venta->save();
                }
            }

            DB::commit();
            return response()->json(['message' => 'ok']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error procesando callback PagoFácil', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'error'], 500);
        }
    }
}
