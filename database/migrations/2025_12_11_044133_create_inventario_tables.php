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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id('codigo_categoria');
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('unidad_medidas', function (Blueprint $table) {
            $table->id('codigo_unidad_medida');
            $table->string('nombre');
            $table->timestamps();
        });

        Schema::create('productos', function (Blueprint $table) {
            $table->id('codigo_producto');
            $table->string('nombre_producto');
            $table->string('imagen_url')->nullable();
            $table->decimal('precio_unitario', 10, 2);
            $table->integer('grupo')->comment('tienda, bebida', 'libreria');
            $table->foreignId('unidad_medida')->nullable()->constrained('unidad_medidas', 'codigo_unidad_medida');
            $table->timestamps();            
        });

         Schema::create('producto_categoria', function (Blueprint $table) {
            $table->id('codigo_producto_categoria');
            $table->foreignId('categoria')->constrained('categorias', 'codigo_categoria');
            $table->foreignId('producto')->constrained('productos', 'codigo_producto');
            $table->unique(['categoria', 'producto']); // Índice compuesto            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_categoria');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('unidad_medidas');
        Schema::dropIfExists('categorias');
    }
};
