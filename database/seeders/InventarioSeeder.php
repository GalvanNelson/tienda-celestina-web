<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\UnidadMedida;
use App\Models\Producto;
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
        $uni = UnidadMedida::create(['nombre' => 'Unidad']);
        $kg  = UnidadMedida::create(['nombre' => 'Kilogramo']);
        $lt  = UnidadMedida::create(['nombre' => 'Litro']);
        $pq  = UnidadMedida::create(['nombre' => 'Paquete']);

        // 2. CREAR CATEGORÍAS
        $cat_bebidas  = Categoria::create(['nombre' => 'Bebidas']);
        $cat_lacteos  = Categoria::create(['nombre' => 'Lácteos']);        
        $cat_limpieza = Categoria::create(['nombre' => 'Limpieza']);
        $cat_carnicos = Categoria::create(['nombre' => 'Cárnicos y Embutidos']);
        $cat_snacks   = Categoria::create(['nombre' => 'Snacks y Golosinas']);
    }
}
