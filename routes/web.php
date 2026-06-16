<?php

use App\Http\Controllers\ChamadoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/chamados', [ChamadoController::class, 'index'])->name('chamados.index');
    Route::get('/chamados/novo', [ChamadoController::class, 'create'])->name('chamados.create');
    Route::post('/chamados', [ChamadoController::class, 'store'])->name('chamados.store');
    Route::get('/chamados/{chamado}', [ChamadoController::class, 'show'])->name('chamados.show');
    Route::patch('/chamados/{chamado}/status', [ChamadoController::class, 'atualizarStatus'])->name('chamados.status');
    Route::patch('/chamados/{chamado}/reatribuir', [ChamadoController::class, 'reatribuir'])->name('chamados.reatribuir');
    Route::get('/dashboard/distribuicao', [DashboardController::class, 'distribuicao'])->name('dashboard.distribuicao');
});

require __DIR__.'/auth.php';
