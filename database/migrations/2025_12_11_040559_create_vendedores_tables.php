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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendedores_tables');
    }
};
