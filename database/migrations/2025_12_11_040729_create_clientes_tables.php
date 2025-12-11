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
        Schema::dropIfExists('clientes_tables');
    }
};
