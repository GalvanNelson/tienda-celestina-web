<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;
use Exception;

class PagoFacilService
{
    protected $baseUrl;
    protected $tokenService;
    protected $tokenSecret;
    protected $callbackUrl;

    public function __construct()
    {
        // Asegúrate de que estas variables estén en tu .env con los valores del Postman
        $this->baseUrl = env('PAGOFACIL_URL'); 
        $this->tokenService = env('PAGOFACIL_TOKEN_SERVICE');
        $this->tokenSecret = env('PAGOFACIL_TOKEN_SECRET');
        $this->callbackUrl = env('PAGOFACIL_CALLBACK_URL');
    }

    public function autenticar()
    {
        $cacheKey = 'pagofacil_token_' . date('Y-m-d');

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = Http::withHeaders([
            'tcTokenService' => $this->tokenService,
            'tcTokenSecret' => $this->tokenSecret,
        ])->post("{$this->baseUrl}/login");

        if ($response->successful()) {
            $data = $response->json();
            
            $token = null;

            // CASO 1: Token directo en 'values' (string)
            if (isset($data['values']) && is_string($data['values'])) {
                $token = $data['values'];
            } 
            // CASO 2: Token dentro de un objeto 'values' (tu caso actual)
            elseif (isset($data['values']) && is_array($data['values'])) {
                // Aquí estaba el problema: la API devuelve "accessToken"
                $token = $data['values']['accessToken'] ?? $data['values']['access_token'] ?? $data['values']['token'] ?? null;
            }
            // CASO 3: Token en la raíz
            elseif (isset($data['token']) && is_string($data['token'])) {
                $token = $data['token'];
            }

            if (!$token || !is_string($token)) {
                // Limpiamos caché por seguridad si falla
                Cache::forget($cacheKey);
                throw new Exception("Error Auth: No se encontró un token válido. Respuesta: " . json_encode($data));
            }

            // Guardar en caché hasta medianoche
            $segundosHastaMedianoche = Carbon::now()->diffInSeconds(Carbon::now()->endOfDay());
            Cache::put($cacheKey, $token, $segundosHastaMedianoche);

            return $token;
        }

        throw new Exception("Error al autenticar con PagoFácil: " . $response->body());
    }

    public function generarQR(array $datosVenta)
    {
        $token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbWFzdGVycXIucGFnb2ZhY2lsLmNvbS5iby9hcGkvc2VydmljZXMvdjIvbG9naW4iLCJpYXQiOjE3NjU5NDg4ODQsImV4cCI6MTc2NjAzMDQwMCwibmJmIjoxNzY1OTQ4ODg0LCJqdGkiOiIyNUJKTVZQYTBIejZHZXdZIiwic3ViIjoiNDciLCJwcnYiOiJlZTMwYTE1M2VhZTA0ZTczZjc5NDA2MzlkYTYwMzRiNzEzOTQ5YmE1In0.v9IvJosb4L2FRnXi01HTH7PEVpztMFf6X_jvoc2Sd6E';        

        // Estructura exacta basada en tu JSON de Postman
        $cuerpoPeticion = [
            "paymentMethod" => 4, // 4 = QR PagoFácil
            "currency" => 2,      // 2 = Bolivianos
            "clientName" => $datosVenta['cliente_nombre'],
            "documentType" => 1,  // 1 = CI
            "documentId" => $datosVenta['cliente_ci'],
            "phoneNumber" => $datosVenta['cliente_telefono'],
            "email" => $datosVenta['cliente_email'],
            "paymentNumber" => $datosVenta['id_transaccion'], // GRUPO14SA_X
            "amount" => $datosVenta['monto'],
            "clientCode" => $datosVenta['cliente_id'],
            
            // URL de callback (Ojo: localhost no funciona para recibir notificaciones reales de la API, 
            // pero sirve para generar el QR). Usa la del postman o una tuya.
            "callbackUrl" => $this->callbackUrl,
            
            "orderDetail" => $datosVenta['detalles']
        ];        
        
        $response = Http::withToken($token)
            ->post("{$this->baseUrl}/generate-qr", $cuerpoPeticion);        
        if ($response->successful()) {
            
            return $response->json();
        }

        throw new Exception("Error al generar QR: " . $response->body());
    }
}