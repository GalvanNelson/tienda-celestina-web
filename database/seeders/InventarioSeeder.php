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
        $cat_abarrotes = Categoria::create(['nombre' => 'Abarrotes']);
        $cat_limpieza = Categoria::create(['nombre' => 'Limpieza']);
        $cat_carnicos = Categoria::create(['nombre' => 'Cárnicos y Embutidos']);
        $cat_snacks   = Categoria::create(['nombre' => 'Snacks y Golosinas']);

        // 3. CREAR 20 PRODUCTOS
        $productos = [
            // BEBIDAS
            [
                'nombre_producto' => 'Coca Cola 2L Original',
                'precio_unitario' => 13.00,
                'stock' => 50,
                'categoria' => $cat_bebidas->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Agua Vital s/gas 2L',
                'precio_unitario' => 8.50,
                'stock' => 30,
                'categoria' => $cat_bebidas->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Cerveza Paceña Botella 620ml',
                'precio_unitario' => 16.00,
                'stock' => 100,
                'categoria' => $cat_bebidas->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Jugo del Valle Durazno 1L',
                'precio_unitario' => 11.00,
                'stock' => 25,
                'categoria' => $cat_bebidas->codigo_categoria,
                'unidad_medida' => $lt->codigo_unidad_medida,
            ],

            // LÁCTEOS
            [
                'nombre_producto' => 'Leche Pil Natural 1L',
                'precio_unitario' => 7.00,
                'stock' => 60,
                'categoria' => $cat_lacteos->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida, // Se vende por sachet (unidad)
            ],
            [
                'nombre_producto' => 'Mantequilla Regia con Sal 200g',
                'precio_unitario' => 12.50,
                'stock' => 20,
                'categoria' => $cat_lacteos->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Yogurt Bebible Pil Frutilla 2L',
                'precio_unitario' => 18.00,
                'stock' => 15,
                'categoria' => $cat_lacteos->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida,
            ],

            // ABARROTES
            [
                'nombre_producto' => 'Arroz Grano de Oro',
                'precio_unitario' => 9.50, // Precio por kilo a granel
                'stock' => 100, // Kilos
                'categoria' => $cat_abarrotes->codigo_categoria,
                'unidad_medida' => $kg->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Aceite Fino 1L',
                'precio_unitario' => 14.00,
                'stock' => 40,
                'categoria' => $cat_abarrotes->codigo_categoria,
                'unidad_medida' => $lt->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Azúcar Guabirá',
                'precio_unitario' => 6.50,
                'stock' => 200, // Kilos
                'categoria' => $cat_abarrotes->codigo_categoria,
                'unidad_medida' => $kg->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Fideo Lazzaroni Cortado',
                'precio_unitario' => 5.50,
                'stock' => 30,
                'categoria' => $cat_abarrotes->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida, // Bolsa
            ],
            [
                'nombre_producto' => 'Harina Famosa 1Kg',
                'precio_unitario' => 8.00,
                'stock' => 25,
                'categoria' => $cat_abarrotes->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida,
            ],

            // LIMPIEZA
            [
                'nombre_producto' => 'Detergente Omo Multiacción 1Kg',
                'precio_unitario' => 22.00,
                'stock' => 20,
                'categoria' => $cat_limpieza->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida, // Bolsa
            ],
            [
                'nombre_producto' => 'Lavandina Ola 1L',
                'precio_unitario' => 6.00,
                'stock' => 30,
                'categoria' => $cat_limpieza->codigo_categoria,
                'unidad_medida' => $lt->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Jabón de Ropa Uno',
                'precio_unitario' => 3.50,
                'stock' => 50,
                'categoria' => $cat_limpieza->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida,
            ],

            // CÁRNICOS
            [
                'nombre_producto' => 'Pollo Entero Sofia',
                'precio_unitario' => 16.50, // Precio por kilo
                'stock' => 40,
                'categoria' => $cat_carnicos->codigo_categoria,
                'unidad_medida' => $kg->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Carne Molida Especial',
                'precio_unitario' => 38.00,
                'stock' => 10,
                'categoria' => $cat_carnicos->codigo_categoria,
                'unidad_medida' => $kg->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Salchichas Sofia Viena (Paq)',
                'precio_unitario' => 25.00,
                'stock' => 15,
                'categoria' => $cat_carnicos->codigo_categoria,
                'unidad_medida' => $pq->codigo_unidad_medida,
            ],

            // SNACKS
            [
                'nombre_producto' => 'Papas Fritas Pringles Pequeña',
                'precio_unitario' => 15.00,
                'stock' => 20,
                'categoria' => $cat_snacks->codigo_categoria,
                'unidad_medida' => $uni->codigo_unidad_medida,
            ],
            [
                'nombre_producto' => 'Galletas Mabel Wafer',
                'precio_unitario' => 4.50,
                'stock' => 40,
                'categoria' => $cat_snacks->codigo_categoria,
                'unidad_medida' => $pq->codigo_unidad_medida,
            ],
        ];

        // Insertar datos masivamente
        foreach ($productos as $prod) {
            Producto::create($prod);
        }

    }
}
