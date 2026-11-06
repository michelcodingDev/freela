<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\ProposalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas de autenticação
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

// Rotas protegidas (requer token de API)
Route::middleware('api.token')->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Projetos
    Route::get('/projetos', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projetos/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projetos/categorias', [ProjectController::class, 'categories'])->name('projects.categories');
    Route::get('/projetos/habilidades', [ProjectController::class, 'skills'])->name('projects.skills');

    // Propostas
    Route::get('/projetos/{project}/propostas', [ProposalController::class, 'index'])->name('proposals.index');
    Route::post('/projetos/{project}/propostas', [ProposalController::class, 'store'])->name('proposals.store');

    // Pagamentos
    Route::post('/projetos/{project}/pagamentos', [PaymentController::class, 'create'])->name('payments.create');
    Route::get('/pagamentos', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/pagamentos/{payment}/liberar', [PaymentController::class, 'release'])->name('payments.release');
});
