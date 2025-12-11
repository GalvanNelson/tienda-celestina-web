<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cliente; // <--- 1. Importamos el modelo Cliente
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // <--- 2. Importamos DB para la transacción
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 3. Usamos una transacción para garantizar que se creen ambos registros
        DB::transaction(function () use ($request) {
            
            // A. Creamos el Usuario (Login)
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // B. Creamos automáticamente el perfil de Cliente
            // Esto es lo que le da el rol 'cliente' en tu sistema
            Cliente::create([
                'user_id' => $user->id,
                'nombre_cliente' => $user->name, // Usamos el mismo nombre del registro
                // 'carnet_identidad' => null, // Opcional, queda null por defecto
            ]);

            event(new Registered($user));

            Auth::login($user);
        });

        // 4. Redirigimos al área de clientes en lugar del dashboard general
        return redirect(route('cliente.home', absolute: false));
    }
}
