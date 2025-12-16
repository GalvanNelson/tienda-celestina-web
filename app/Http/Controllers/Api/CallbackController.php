<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DetallePagoOnline;
use App\Models\Pago;
use App\Models\Venta;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CallbackController extends Controller
{
    public function procesarPago(Request $request)
    {
        // 1. Guardamos el log para ver qué nos envía PagoFácil exactamente
        Log::info('Callback PagoFácil Recibido:', $request->all());

        try {
            DB::beginTransaction();

            // 2. Validación estricta con los nombres de campo que me diste
            $data = $request->validate([
                'PedidoID'   => 'required', // Ej: GRUPO14SA_105
                'Fecha'      => 'required',
                'Hora'       => 'required',
                'MetodoPago' => 'required',
                'Estado'     => 'required'  
            ]);

            // 3. Buscamos la transacción en nuestra BD por el PedidoID
            $detalleOnline = DetallePagoOnline::where('pedido_id', $data['PedidoID'])->first();

            if (!$detalleOnline) {
                return response()->json([
                    "error" => 1,
                    "status" => 0,
                    "message" => "Pedido no encontrado en el sistema",
                    "values" => false
                ], 404);
            }

            // 4. Actualizamos los datos del detalle online con la info que llegó
            $detalleOnline->update([
                'fecha_transaccion'    => $data['Fecha'],
                'hora_transaccion'     => $data['Hora'],
                'metodo_pago_pasarela' => strval($data['MetodoPago']),
                'estado_transaccion'   => strval($data['Estado'])
            ]);

            // 5. Lógica para marcar como PAGADO
            // PagoFácil suele mandar Estado = 2 para completado. 
            // Si tu Estado llega como texto "COMPLETADO", ajústalo aquí.
            $estadoRecibido = strval($data['Estado']);
            
            // Verificamos si es un estado de éxito (2 o 'COMPLETADO' o 'PAGADO')
            $esExitoso = ($estadoRecibido === '2' || strtoupper($estadoRecibido) === 'COMPLETADO' || strtoupper($estadoRecibido) === 'PAGADO');

            if ($esExitoso) {
                // A. Actualizar Pago (si existe)
                $pago = Pago::find($detalleOnline->pago);
                if ($pago) {
                    // Si el monto estaba en 0, podrías actualizarlo aquí si tuvieras el monto en el request.
                    // Por ahora asumimos que el QR se generó por el monto correcto.
                    
                    // B. Actualizar Venta a "pagado"
                    $venta = Venta::find($pago->venta);
                    if ($venta) {
                        $venta->update(['estado_venta' => 'pagado']);
                    }
                }
            }

            DB::commit();

            // 6. RESPUESTA EXACTA REQUERIDA
            return response()->json([
                "error" => 0,
                "status" => 1,
                "message" => "Pago procesado correctamente",
                "values" => true
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en Callback: ' . $e->getMessage());
            
            // Respuesta de error manteniendo el formato
            return response()->json([
                "error" => 1,
                "status" => 0,
                "message" => "Error interno al procesar el pago: " . $e->getMessage(),
                "values" => false
            ], 500);
        }
    }
}
