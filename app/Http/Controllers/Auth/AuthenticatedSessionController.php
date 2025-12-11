<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Leemos el rol del usuario
        $role = $request->user()->role_type;

        // 2. Decidimos a dónde enviarlo según su rol
        switch ($role) {
            case 'propietario':
                // Si es propietario, va a su panel de control
                return redirect()->intended(route('admin.dashboard', absolute: false));
            
            case 'vendedor':
                // Si es vendedor, va directo a registrar ventas
                return redirect()->intended(route('ventas.create', absolute: false));

            case 'cliente':
                // Si es cliente, va a su perfil de compras
                return redirect()->intended(route('cliente.home', absolute: false));

            default:
                // Si no tiene rol específico (o es admin global), va al dashboard por defecto
                return redirect()->intended(route('dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
