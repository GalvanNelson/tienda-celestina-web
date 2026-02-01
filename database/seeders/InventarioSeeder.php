<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Categoria, UnidadMedida, Producto};
use Illuminate\Support\Facades\DB;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desactiva las restricciones de llave foránea temporalmente
        DB::statement('SET CONSTRAINTS ALL DEFERRED;');

        Producto::truncate();
        Categoria::truncate();
        UnidadMedida::truncate();

        // 1. CREAR UNIDADES DE MEDIDA
        UnidadMedida::create(['nombre' => 'Unidad']);
        UnidadMedida::create(['nombre' => 'Kilogramo']);
        UnidadMedida::create(['nombre' => 'Litro']);
        UnidadMedida::create(['nombre' => 'Paquete']);

        // 2. CREAR CATEGORÍAS
        Categoria::create(['nombre' => 'Bebidas']);
        Categoria::create(['nombre' => 'Lácteos']);        
        Categoria::create(['nombre' => 'Limpieza']);
        Categoria::create(['nombre' => 'Cárnicos']);
        Categoria::create(['nombre' => 'Snacks']);
        Categoria::create(['nombre' => 'Embutidos']);
        Categoria::create(['nombre' => 'Golosinas']);

        Producto::create([
            'nombre_producto' => 'Leche Entera 1L',
            'precio_unitario' => 1.20,            
            'grupo' => 2, // Lácteos
            'unidad_medida' => 1, // Unidad
        ]);
        Producto::create([
            'nombre_producto' => 'Jugo de Naranja 500ml',
            'precio_unitario' => 0.80,            
            'grupo' => 1, // Bebidas
            'unidad_medida' => 1, // Unidad
        ]);
        Producto::create([
            'nombre_producto' => 'Detergente Líquido 1L',
            'precio_unitario' => 3.50,            
            'grupo' => 3, // Limpieza
            'unidad_medida' => 1, // Unidad
        ]);        
    }
}
