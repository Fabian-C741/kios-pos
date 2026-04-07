<?php

namespace App\Services;

use App\Models\Venta;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected ?string $token;
    protected ?string $phoneNumberId;

    public function __construct()
    {
        $this->token = config('services.whatsapp.token') ?? null;
        $this->phoneNumberId = config('services.whatsapp.phone_number_id') ?? null;
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
        $mensaje = "*TIQUETE DE VENTA*\n\n";
        $mensaje .= "--------------------------\n";
        $mensaje .= "N: {$venta->numero_venta}\n";
        $mensaje .= "Fecha: {$venta->fecha_venta->format('d/m/Y H:i')}\n";
        $mensaje .= "--------------------------\n\n";
        
        foreach ($venta->detalles as $detalle) {
            $mensaje .= "- {$detalle->producto->nombre}\n";
            $mensaje .= "  {$detalle->cantidad} x $" . number_format($detalle->precio_unitario, 2) . " = $" . number_format($detalle->subtotal, 2) . "\n";
        }
        
        $mensaje .= "\n--------------------------\n";
        $mensaje .= "TOTAL: $" . number_format($venta->total, 2) . "\n";
        $mensaje .= "Metodo: " . ucfirst($venta->metodo_pago) . "\n";
        $mensaje .= "\nGracias por su compra!\n";
        
        return $mensaje;
    }

    public function enviarMensaje(string $telefono, string $mensaje): array
    {
        if (empty($this->token) || empty($this->phoneNumberId)) {
            return ['success' => false, 'error' => 'WhatsApp API no configurada'];
        }

        try {
            $response = Http::withToken($this->token)
                ->post("https://graph.facebook.com/v18.0/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $telefono,
                    'type' => 'text',
                    'text' => ['body' => $mensaje],
                ]);

            if ($response->successful()) {
                return ['success' => true, 'message_id' => $response->json('messages.0.id')];
            }

            return ['success' => false, 'error' => $response->json('error.message', 'Error desconocido')];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
