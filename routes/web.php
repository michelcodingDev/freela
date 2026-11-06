<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\UserPlanSubscriptionController;
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

// Webhook do PagSeguro (não requer autenticação)
Route::post('/webhook/pagseguro/{method}', [PaymentController::class, 'webhook'])->name('payments.webhook');

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

    // Propostas
    Route::get('/projetos/{project}/propostas', [ProposalController::class, 'index'])->name('proposals.index');
    Route::get('/projetos/{project}/propostas/enviar', [ProposalController::class, 'create'])->name('proposals.create');
    Route::post('/projetos/{project}/propostas', [ProposalController::class, 'store'])->name('proposals.store');
    Route::post('/projetos/{project}/propostas/{proposal}/aceitar', [ProposalController::class, 'accept'])->name('proposals.accept');
    Route::post('/projetos/{project}/propostas/{proposal}/rejeitar', [ProposalController::class, 'reject'])->name('proposals.reject');

    // Pagamentos
    Route::get('/pagamentos/processar/{project}', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('/pagamentos/boleto/{payment}', [PaymentController::class, 'boleto'])->name('payments.boleto');
    Route::post('/pagamentos/liberar/{payment}', [PaymentController::class, 'release'])->name('payments.release');

    // Admin: Pagamentos
    Route::middleware(['can:ver todos pagamentos'])->group(function () {
        Route::get('/admin/pagamentos', [PaymentController::class, 'adminIndex'])->name('admin.payments.index');
        Route::get('/admin/pagamentos/{payment}', [PaymentController::class, 'adminShow'])->name('admin.payments.show');
    });

    // Planos (Admin)
    Route::middleware(['can:gerenciar planos'])->prefix('admin/planos')->name('admin.plans.')->group(function () {
        Route::get('/', [PlanController::class, 'index'])->name('index');
        Route::get('/criar', [PlanController::class, 'create'])->name('create');
        Route::post('/', [PlanController::class, 'store'])->name('store');
        Route::get('/{plan}', [PlanController::class, 'show'])->name('show');
        Route::get('/{plan}/editar', [PlanController::class, 'edit'])->name('edit');
        Route::put('/{plan}', [PlanController::class, 'update'])->name('update');
        Route::delete('/{plan}', [PlanController::class, 'destroy'])->name('destroy');
        Route::post('/{plan}/toggle-status', [PlanController::class, 'toggleStatus'])->name('toggle');
    });

    // Assinaturas do Usuário
    Route::get('/assinaturas', [UserPlanSubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('/assinaturas/{plan}', [UserPlanSubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::post('/assinaturas/cancelar', [UserPlanSubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    // Admin: Assinaturas
    Route::middleware(['can:gerenciar planos'])->prefix('admin/assinaturas')->name('admin.subscriptions.')->group(function () {
        Route::get('/', [UserPlanSubscriptionController::class, 'adminIndex'])->name('index');
    });

    // Dashboard Admin (apenas admin)
    Route::middleware(['can:gerenciar planos'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/usuarios', [AdminDashboardController::class, 'users'])->name('users');
        Route::get('/pagamentos', [AdminDashboardController::class, 'payments'])->name('payments');
        Route::get('/projetos', [AdminDashboardController::class, 'projects'])->name('projects');
        Route::get('/disputas', [AdminDashboardController::class, 'disputes'])->name('disputes');
    });

    // Logs de Auditoria (apenas admin)
    Route::middleware(['can:gerenciar planos'])->prefix('admin/logs')->name('admin.logs.')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::get('/erros', [LogController::class, 'errors'])->name('errors');
        Route::get('/exportar', [LogController::class, 'exportCsv'])->name('export');
    });
});
