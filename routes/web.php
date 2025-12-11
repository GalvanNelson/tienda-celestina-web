<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// 1. Ruta Pública (Bienvenida)
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// 2. Ruta Dashboard General (Fallback por si alguien entra sin rol específico)
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 3. Rutas de Perfil (Para todos los usuarios logueados)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// -----------------------------------------------------------------------------
//  RUTAS POR ROLES (Aquí conectamos con AuthenticatedSessionController)
// -----------------------------------------------------------------------------

// ZONA PROPIETARIOS
// Solo accesible si role_type === 'propietario'
Route::middleware(['auth', 'verified', 'role:propietario'])->group(function () {
    
    Route::get('/admin/dashboard', function () {
        return Inertia::render('Admin/Dashboard'); 
    })->name('admin.dashboard');

    // Aquí podrás agregar más rutas como:
    // Route::get('/admin/reportes', ...)->name('admin.reportes');
});

// ZONA VENTAS (Vendedores y Propietarios)
// Accesible si role_type es 'vendedor' O 'propietario'
Route::middleware(['auth', 'verified', 'role:propietario,vendedor'])->group(function () {
    
    Route::get('/ventas/nueva', function () {
        return Inertia::render('Ventas/Create');
    })->name('ventas.create');
    
    // Aquí irían rutas para ver productos, clientes, etc.
});

// ZONA CLIENTES
// Solo accesible si role_type === 'cliente'
Route::middleware(['auth', 'verified', 'role:cliente'])->group(function () {
    
    Route::get('/mi-cuenta', function () {
        return Inertia::render('Cliente/Home');
    })->name('cliente.home');

});

require __DIR__.'/auth.php';
