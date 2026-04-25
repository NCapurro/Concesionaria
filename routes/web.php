<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\AdminMotoController;


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // La vista que armaste recién
    Route::get('/motos', [AdminMotoController::class, 'index'])->name('motos.index');
    
    // Las acciones del formulario
    Route::post('/motos', [AdminMotoController::class, 'store'])->name('motos.store');
    Route::put('/motos/{id}', [AdminMotoController::class, 'update'])->name('motos.update');
    Route::delete('/motos/{id}', [AdminMotoController::class, 'destroy'])->name('motos.destroy');
});

// Ruta principal que alimenta el landing page
Route::get('/', [HomeController::class, 'index'])->name('home');

// Rutas placeholder para que los links de tu template no rompan
Route::get('/catalogo', function () {
    return 'Página de catálogo completo en construcción';
})->name('motos.index');

Route::get('/moto/{id}', function ($id) {
    return "Detalle de la moto ID: $id en construcción";
})->name('motos.show');

Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');

require __DIR__.'/auth.php';
