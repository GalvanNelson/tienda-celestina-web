<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\VendedorController;
use App\Http\Controllers\ClienteController;
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
Route::middleware(['auth', 'verified', 'role:propietario'])
    ->prefix('propietario') // <--- AGREGA ESTA LÍNEA
    ->group(function () {
    
    Route::get('/dashboard', function () { // Puedes simplificar esto si usas el prefijo
        return Inertia::render('Propietario/Dashboard'); 
    })->name('propietario.dashboard');

    // Ahora la URL será /propietario/productos
    Route::resource('productos', ProductoController::class);
    Route::resource('categorias', CategoriaController::class);
    Route::resource('unidades', UnidadMedidaController::class);
    Route::resource('vendedores', VendedorController::class);
    Route::resource('clientes', ClienteController::class);
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
