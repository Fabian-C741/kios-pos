<?php

namespace App\Services;

use App\Models\Venta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected string $token;
    protected string $phoneNumberId;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    public function enviarTicket(Venta $venta, string $telefono): array
    {
        $telefono = $this->formatearTelefono($telefono);

        $mensaje = $this->generarMensajeTicket($venta);

        return $this->enviarMensaje($telefono, $mensaje);
    }

    protected function formatearTelefono(string $telefono): string
    {
        $telefono = preg_replace('/[^0-9]/', '', $telefono);
        
        if (strlen($telefono) == 10) {
            $telefono = '52' . $telefono;
        }
        
        if (strlen($telefono) == 9) {
            $telefono = '521' . $telefono;
        }

        return $telefono;
    }

    protected function generarMensajeTicket(Venta $venta): string
    {
        $mensaje = "🧾 *TIQUETE DE VENTA*\n\n";
        $mensaje .= "━━━━━━━━━━━━━━━━━━\n";
        $mensaje .= "*N°:* {$venta->numero_venta}\n";
        $mensaje .= "*Fecha:* {$venta->fecha_venta->format('d/m/Y H:i')}\n";
        $mensaje .= "━━━━━━━━━━━━━━━━━━\n\n";
        
        foreach ($venta->detalles as $detalle) {
            $mensaje .= "• {$detalle->producto->nombre}\n";
            $mensaje .= "  {$detalle->cantidad} x \$" . number_format($detalle->precio_unitario, 2) . " = \$" . number_format($detalle->subtotal, 2) . "\n";
        }
        
        $mensaje .= "\n━━━━━━━━━━━━━━━━━━\n";
        $mensaje .= "*Subtotal:* \$" . number_format($venta->total + $venta->descuento, 2) . "\n";
        
        if ($venta->descuento > 0) {
            $mensaje .= "*Descuento:* -\$" . number_format($venta->descuento, 2) . "\n";
        }
        
        $mensaje .= "*TOTAL:* \$" . number_format($venta->total, 2) . "\n";
        $mensaje .= "*Método:* " . ucfirst($venta->metodo_pago) . "\n";
        
        if ($venta->efectivo_recibido) {
            $mensaje .= "*Efectivo:* \$" . number_format($venta->efectivo_recibido, 2) . "\n";
            $mensaje .= "*Cambio:* \$" . number_format($venta->cambio, 2) . "\n";
        }
        
        $mensaje .= "\n━━━━━━━━━━━━━━━━━━\n";
        $mensaje .= "¡Gracias por su compra! 🎉\n";
        
        return $mensaje;
    }

    public function enviarMensaje(string $telefono, string $mensaje): array
    {
        if (empty($this->token) || empty($this->phoneNumberId)) {
            return [
                'success' => false,
                'error' => 'WhatsApp API no configurada. Complete WHATSAPP_BUSINESS_TOKEN y WHATSAPP_PHONE_NUMBER_ID en .env',
            ];
        }

        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v18.0/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $telefono,
                    'type' => 'text',
                    'text' => [
                        'body' => $mensaje,
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json('messages.0.id'),
                ];
            }

            Log::error('WhatsApp API Error', ['response' => $response->json()]);

            return [
                'success' => false,
                'error' => $response->json('error.message', 'Error desconocido'),
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp Exception', ['exception' => $e->getMessage()]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function enviarNotificacionStock(string $telefono, array $productos): array
    {
        $telefono = $this->formatearTelefono($telefono);

        $mensaje = "⚠️ *ALERTA DE STOCK BAJO*\n\n";
        $mensaje .= "Los siguientes productos tienen stock bajo:\n\n";

        foreach ($productos as $producto) {
            $mensaje .= "• {$producto['nombre']}\n";
            $mensaje .= "  Stock actual: {$producto['stock']} (Mín: {$producto['stock_minimo']})\n\n";
        }

        $mensaje .= "━━━━━━━━━━━━━━━━━━\n";
        $mensaje .= "Por favor revisa el inventario.";

        return $this->enviarMensaje($telefono, $mensaje);
    }
}
