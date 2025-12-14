<?php

namespace App\Http\Controllers;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::with('user')
            ->latest()
            ->paginate(10);

        return Inertia::render('Propietario/Clientes/Index', [
            'clientes' => $clientes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Propietario/Clientes/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            // Datos de Usuario
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            
            // Datos de Cliente
            'carnet_identidad' => 'required|string|max:20',            
        ]);

        DB::transaction(function () use ($request) {
            // 1. Crear el Usuario
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role_type' => 'cliente', // Asumimos que tienes este campo para roles
            ]);

            // 2. Crear el Perfil Cliente vinculado
            Cliente::create([
                'user_id' => $user->id,
                'nombre_cliente' => $request->name,
                'carnet_identidad' => $request->carnet_identidad,
            ]);
        });

        return redirect()->route('clientes.index')->with('success', 'Cliente creado.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Cargamos el cliente y su usuario
        $cliente = Cliente::with('user')->findOrFail($id);
        
        return Inertia::render('Propietario/Clientes/Edit', [
            'cliente' => $cliente
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente = Cliente::with('user')->findOrFail($id);
        $user = $cliente->user;

        $request->validate([
            // Datos de Usuario
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            // Datos de Cliente
            'carnet_identidad' => 'required|string|max:20'            
        ]);

        DB::transaction(function () use ($request, $cliente, $user) {
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
        
            // Actualizar Cliente
            $cliente->update([
                'nombre_cliente' => $request->name,
                'carnet_identidad' => $request->carnet_identidad,  
                'limite_credito' => $request->limite_credito ?? $cliente->limite_credito,         
            ]);
        });

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $user = $cliente->user;

         // Verificar si el cliente tiene saldo pendiente        
        if ($cliente->saldo_actual > 0) {
            return redirect()->route('clientes.index')->with('error', 'No se puede eliminar un cliente con saldo pendiente.');
        }

        DB::transaction(function () use ($cliente, $user) {
            // Primero eliminamos el perfil de cliente
            $cliente->delete();
            // Luego eliminamos el usuario asociado
            $user->delete();
        });

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado.');
    }
}
