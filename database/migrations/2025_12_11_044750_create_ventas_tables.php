<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ventas
        Schema::create('ventas', function (Blueprint $table) {
            $table->id('codigo_venta');
            
            $table->foreignId('cliente')->constrained('clientes', 'codigo_cliente');
            $table->foreignId('vendedor')->constrained('vendedores', 'codigo_vendedor');
            
            $table->timestamp('fecha_venta');
            $table->decimal('monto_total', 10, 2);
            $table->string('tipo_pago'); // 'contado', 'credito'
            $table->string('estado_venta'); // 'pagado', 'pendiente'
            $table->decimal('interes_aplicado', 5, 2)->default(0);
            $table->timestamps();
        });

        // Detalle Ventas
        Schema::create('detalle_ventas', function (Blueprint $table) {
            $table->id('codigo_detalle_venta');
            $table->foreignId('venta')->constrained('ventas', 'codigo_venta')->onDelete('cascade');
            $table->foreignId('producto')->constrained('productos', 'codigo_producto');
            $table->decimal('precio_unitario', 10, 2);
            $table->decimal('cantidad', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // Cuotas
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id('codigo_cuota');
            $table->foreignId('venta')->constrained('ventas', 'codigo_venta')->onDelete('cascade');
            $table->integer('numero_cuota');
            $table->date('fecha_vencimiento');
            $table->decimal('monto_programado', 10, 2);
            $table->decimal('monto_abonado', 10, 2)->default(0);
            $table->string('estado_cuota')->default('pendiente');
            $table->timestamps();
        });

        // Pagos
        Schema::create('pagos', function (Blueprint $table) {
            $table->id('codigo_pago');
            $table->foreignId('venta')->constrained('ventas', 'codigo_venta');
            // Nullable porque al contado no hay cuota
            $table->foreignId('cuota')->nullable()->constrained('cuotas', 'codigo_cuota');
            $table->timestamp('fecha_pago');
            $table->decimal('monto_pagado', 10, 2);
            $table->string('metodo_pago');
            $table->timestamps();
        });
        
        // Detalles Pago Online (Fassil)
        Schema::create('detalles_pago_online', function (Blueprint $table) {
            // Asumiendo que viene de API y no es autoincrementable
            $table->id('codigo_pago_online');
            $table->foreignId('pago')->constrained('pagos', 'codigo_pago')->onDelete('cascade');
            $table->string('pedido_id');
            $table->date('fecha_transaccion');
            $table->time('hora_transaccion');
            $table->string('metodo_pago_pasarela');
            $table->string('estado_transaccion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
        Schema::dropIfExists('detalle_ventas');
        Schema::dropIfExists('cuotas');
        Schema::dropIfExists('pagos');
        Schema::dropIfExists('detalles_pago_online');
    }
};
