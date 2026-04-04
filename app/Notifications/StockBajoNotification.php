<?php

namespace App\Notifications;

use App\Models\Producto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockBajoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected Producto $producto;

    public function __construct(Producto $producto)
    {
        $this->producto = $producto;
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('⚠️ Alerta: Stock Bajo - ' . $this->producto->nombre)
            ->greeting('Hola ' . $notifiable->name . ',')
            ->line('El producto **' . $this->producto->nombre . '** tiene stock bajo.')
            ->line('**Stock actual:** ' . $this->producto->stock . ' unidades')
            ->line('**Stock mínimo:** ' . $this->producto->stock_minimo . ' unidades')
            ->action('Ver Producto', url('/productos/' . $this->producto->id))
            ->line('Por favor, reponga el inventario lo antes posible.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'producto_id' => $this->producto->id,
            'producto_nombre' => $this->producto->nombre,
            'stock_actual' => $this->producto->stock,
            'stock_minimo' => $this->producto->stock_minimo,
            'mensaje' => "Stock bajo: {$this->producto->nombre} ({$this->producto->stock} unidades)",
        ];
    }
}
