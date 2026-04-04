<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'user_id',
        'saldo_inicial',
        'saldo_actual',
        'abierta',
        'fecha_apertura',
        'fecha_cierre',
    ];

    protected $casts = [
        'saldo_inicial' => 'decimal:2',
        'saldo_actual' => 'decimal:2',
        'abierta' => 'boolean',
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class);
    }

    public function abrir(User $user, float $saldoInicial = 0): void
    {
        $this->update([
            'user_id' => $user->id,
            'abierta' => true,
            'saldo_inicial' => $saldoInicial,
            'saldo_actual' => $saldoInicial,
            'fecha_apertura' => now(),
            'fecha_cierre' => null,
        ]);
    }

    public function cerrar(): void
    {
        $this->update([
            'abierta' => false,
            'fecha_cierre' => now(),
        ]);
    }

    public function agregarMonto(float $monto, string $concepto, ?int $ventaId = null): void
    {
        $this->update(['saldo_actual' => $this->saldo_actual + $monto]);
        
        $this->movimientos()->create([
            'user_id' => $this->user_id,
            'tipo' => 'entrada',
            'monto' => $monto,
            'concepto' => $concepto,
            'venta_id' => $ventaId,
        ]);
    }

    public function quitarMonto(float $monto, string $concepto): void
    {
        $this->update(['saldo_actual' => $this->saldo_actual - $monto]);
        
        $this->movimientos()->create([
            'user_id' => $this->user_id,
            'tipo' => 'salida',
            'monto' => $monto,
            'concepto' => $concepto,
        ]);
    }
}
