<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Rotas de Autenticação
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas protegidas (requer autenticação)
Route::middleware(['auth', 'status:ativo'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Categorias (públicas)
    Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categorias/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // Projetos
    Route::get('/projetos', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projetos/criar', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projetos', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projetos/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projetos/{project}/editar', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projetos/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('/projetos/{project}/fechar', [ProjectController::class, 'close'])->name('projects.close');
});
