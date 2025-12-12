<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Propietario;
use App\Models\Vendedor;
use App\Models\Cliente;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // =================================================================
        // 1. USUARIO PROPIETARIO (ADMIN)
        // =================================================================
        $adminUser = User::create([
            'name' => 'Juan Propietario',
            'email' => 'admin@tienda.com',
            'password' => Hash::make('password'), // La contraseña es 'password'
        ]);

        Propietario::create([
            'user_id' => $adminUser->id,
            'razon_social' => 'Tienda Celestina S.A.',
            'nit_o_ruc' => '1020304050',
            'direccion_oficina' => 'Av. Banzer 4to Anillo',
            'telefono_contacto' => '70012345'
        ]);

        // =================================================================
        // 2. USUARIO VENDEDOR
        // =================================================================
        $vendedorUser = User::create([
            'name' => 'Ana Vendedora',
            'email' => 'vendedor@tienda.com',
            'password' => Hash::make('password'),
        ]);

        Vendedor::create([
            'user_id' => $vendedorUser->id,
            'carnet_identidad' => '88776655',
            'telefono' => '60090080',
            'sueldo_basico' => 2500.00,
            'porcentaje_comision' => 3.50, // 3.5%
            'fecha_contratacion' => '2024-01-15',
            'estado' => 'activo'
        ]);

        // =================================================================
        // 3. USUARIO CLIENTE
        // =================================================================
        $clienteUser = User::create([
            'name' => 'Carlos Cliente',
            'email' => 'cliente@tienda.com',
            'password' => Hash::make('password'),
        ]);

        Cliente::create([
            'user_id' => $clienteUser->id,
            'nombre_cliente' => 'Carlos Cliente',
            'carnet_identidad' => '1234567-SC',
            'limite_credito' => 5000.00,
            'saldo_actual' => 0.00
        ]);
        
        $this->call(InventarioSeeder::class);
    }
}
