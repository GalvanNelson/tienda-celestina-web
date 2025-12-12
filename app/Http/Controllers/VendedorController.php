<?php

namespace App\Http\Controllers;
use App\Models\Vendedor;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VendedorController extends Controller
{
    public function index()
    {
        // Traemos el vendedor CON su usuario asociado para mostrar el nombre y email
        $vendedores = Vendedor::with('user')
            ->latest()
            ->paginate(10);

        return Inertia::render('Propietario/Vendedores/Index', [
            'vendedores' => $vendedores
        ]);
    }

    public function create()
    {
        return Inertia::render('Propietario/Vendedores/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // Datos de Usuario
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            
            // Datos de Vendedor
            'carnet_identidad' => 'required|string|max:20',
            'telefono' => 'required|string|max:20',
            'sueldo_basico' => 'required|numeric|min:0',
            'porcentaje_comision' => 'required|numeric|min:0|max:100',
            'fecha_contratacion' => 'required|date',
        ]);

        DB::transaction(function () use ($request) {
            // 1. Crear el Usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_type' => 'vendedor', // Asumimos que tienes este campo para roles
            ]);

            // 2. Crear el Perfil Vendedor vinculado
            Vendedor::create([
                'user_id' => $user->id,
                'carnet_identidad' => $request->carnet_identidad,
                'telefono' => $request->telefono,
                'sueldo_basico' => $request->sueldo_basico,
                'porcentaje_comision' => $request->porcentaje_comision,
                'fecha_contratacion' => $request->fecha_contratacion,
                'estado' => 'activo',
            ]);
        });

        return redirect()->route('vendedores.index')->with('success', 'Vendedor registrado correctamente.');
    }

    public function edit($id)
    {
        // Cargamos el vendedor y su usuario
        $vendedor = Vendedor::with('user')->findOrFail($id);
        
        return Inertia::render('Propietario/Vendedores/Edit', [
            'vendedor' => $vendedor
        ]);
    }

    public function update(Request $request, $id)
    {
        $vendedor = Vendedor::findOrFail($id);
        $user = $vendedor->user;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'carnet_identidad' => 'required|string|max:20',
            'telefono' => 'required|string|max:20',
            'sueldo_basico' => 'required|numeric|min:0',
            'porcentaje_comision' => 'required|numeric|min:0|max:100',
            'fecha_contratacion' => 'required|date',
            'estado' => 'required|in:activo,inactivo',
        ]);

        DB::transaction(function () use ($request, $vendedor, $user) {
            // Actualizar Usuario
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
            ];
            // Solo actualizamos password si enviaron algo
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $user->update($userData);

            // Actualizar Vendedor
            $vendedor->update([
                'carnet_identidad' => $request->carnet_identidad,
                'telefono' => $request->telefono,
                'sueldo_basico' => $request->sueldo_basico,
                'porcentaje_comision' => $request->porcentaje_comision,
                'fecha_contratacion' => $request->fecha_contratacion,
                'estado' => $request->estado,
            ]);
        });

        return redirect()->route('vendedores.index')->with('success', 'Datos del vendedor actualizados.');
    }

    public function destroy($id)
    {
        $vendedor = Vendedor::findOrFail($id);
        
        // Al borrar el usuario, la BD borrará el vendedor automáticamente (cascade)
        // Pero lo hacemos explícito buscando al usuario
        $user = $vendedor->user;
        $user->delete();

        return redirect()->back()->with('success', 'Vendedor eliminado y acceso revocado.');
    }
}
