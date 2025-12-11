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
        Schema::create('propietarios', function (Blueprint $table) {
            $table->id('codigo_propietario');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('razon_social');
            $table->string('nit_o_ruc');
            $table->string('direccion_oficina')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->timestamps();
        });

        Schema::create('vendedores', function (Blueprint $table) {
            $table->id('codigo_vendedor');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('carnet_identidad');
            $table->string('telefono');
            $table->decimal('sueldo_basico', 10, 2);
            $table->decimal('porcentaje_comision', 5, 2);
            $table->date('fecha_contratacion');
            $table->string('estado')->default('activo');
            $table->timestamps();
        });

        Schema::create('clientes', function (Blueprint $table) {
            $table->id('codigo_cliente');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nombre_cliente');
            $table->string('carnet_identidad')->nullable();
            $table->decimal('limite_credito', 10, 2)->default(0);
            $table->decimal('saldo_actual', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {        
        Schema::dropIfExists('propietarios');
        Schema::dropIfExists('vendedores');
        Schema::dropIfExists('clientes');
    }
};
