<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'stock',
        'stock_minimo',
        'codigo_barras',
        'codigo_qr',
        'categoria',
        'imagen',
        'activo',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'stock' => 'decimal:3',
        'stock_minimo' => 'decimal:3',
        'activo' => 'boolean',
    ];

    public function detalleVentas(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function scopeStockBajo($query)
    {
        return $query->whereColumn('stock', '<=', 'stock_minimo');
    }

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function getStockBajoAttribute(): bool
    {
        return $this->stock <= $this->stock_minimo;
    }

    public function getTotalVentasAttribute(): int
    {
        return $this->detalleVentas()->sum('cantidad');
    }
}
