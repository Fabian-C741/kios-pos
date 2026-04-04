<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('total', 10, 2);
            $table->decimal('descuento', 10, 2)->default(0);
            $table->enum('metodo_pago', ['efectivo', 'tarjeta', 'transferencia', 'mixto'])->default('efectivo');
            $table->decimal('efectivo_recibido', 10, 2)->nullable();
            $table->decimal('cambio', 10, 2)->nullable();
            $table->string('notas')->nullable();
            $table->enum('estado', ['completada', 'cancelada', 'pendiente'])->default('completada');
            $table->timestamp('fecha_venta')->useCurrent();
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('fecha_venta');
            $table->index('estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
