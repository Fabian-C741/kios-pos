<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('codigo_barras')->unique()->nullable();
            $table->string('codigo_qr')->nullable();
            $table->string('categoria')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('codigo_barras');
            $table->index('stock');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
