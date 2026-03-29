<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;


Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
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
