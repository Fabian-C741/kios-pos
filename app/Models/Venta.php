<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'descuento',
        'metodo_pago',
        'efectivo_recibido',
        'cambio',
        'notas',
        'estado',
        'fecha_venta',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        'descuento' => 'decimal:2',
        'efectivo_recibido' => 'decimal:2',
        'cambio' => 'decimal:2',
        'fecha_venta' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function productos()
    {
        return $this->belongsToMany(Producto::class, 'detalle_ventas')
            ->withPivot(['cantidad', 'precio_unitario', 'subtotal', 'descuento'])
            ->withTimestamps();
    }

    public function scopeCompletadas($query)
    {
        return $query->where('estado', 'completada');
    }

    public function scopeDelDia($query)
    {
        return $query->whereDate('fecha_venta', today());
    }

    public function getTotalProductosAttribute(): int
    {
        return $this->detalles()->sum('cantidad');
    }

    public function getNumeroVentaAttribute(): string
    {
        return 'V-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
