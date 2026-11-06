<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\FreelancerController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlanController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectInviteController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\UserPlanSubscriptionController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\WithdrawalController;
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
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'updatePassword'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Webhook do PagSeguro (não requer autenticação)
Route::post('/webhook/pagseguro/{method}', [PaymentController::class, 'webhook'])->name('payments.webhook');

// Rotas protegidas (requer autenticação)
Route::middleware(['auth', 'status:ativo'])->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Notificações
    Route::get('/notificacoes', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificacoes/{notification}/ler', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notificacoes/ler-todas', [NotificationController::class, 'readAll'])->name('notifications.readAll');

    // Perfil do usuário
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Categorias (públicas)
    Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categorias/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // Freelancers (vitrine pública)
    Route::get('/freelancers', [FreelancerController::class, 'index'])->name('freelancers.index');
    Route::get('/freelancers/{freelancer}', [FreelancerController::class, 'show'])->name('freelancers.show');
    Route::get('/ranking', RankingController::class)->name('ranking.index');

    // Projetos
    Route::get('/projetos', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projetos/criar', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/projetos', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projetos/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/projetos/{project}/editar', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/projetos/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('/projetos/{project}/fechar', [ProjectController::class, 'close'])->name('projects.close');
    Route::post('/projetos/{project}/reabrir', [ProjectController::class, 'reopen'])->name('projects.reopen');
    Route::post('/projetos/{project}/turbinar', [ProjectController::class, 'boost'])->name('projects.boost');
    Route::post('/projetos/{project}/aprovar', [ProjectController::class, 'approve'])->name('projects.approve');
    Route::post('/projetos/{project}/reprovar', [ProjectController::class, 'reject'])->name('projects.reject');

    // Anexos (download seguro)
    Route::get('/anexos/{attachment}', [AttachmentController::class, 'download'])->name('attachments.download');

    // Convites (RF04)
    Route::post('/projetos/{project}/convidar', [ProjectInviteController::class, 'store'])->name('projects.invites.store');
    Route::get('/convites', [ProjectInviteController::class, 'index'])->name('invites.index');
    Route::post('/convites/{invite}/aceitar', [ProjectInviteController::class, 'accept'])->name('invites.accept');
    Route::post('/convites/{invite}/recusar', [ProjectInviteController::class, 'decline'])->name('invites.decline');

    // Chat por projeto
    Route::get('/projetos/{project}/mensagens', [MessageController::class, 'index'])->name('projects.messages.index');
    Route::post('/projetos/{project}/mensagens', [MessageController::class, 'store'])->name('projects.messages.store');

    // Entregas / execução
    Route::post('/projetos/{project}/iniciar', [DeliveryController::class, 'start'])->name('projects.start');
    Route::post('/projetos/{project}/entregar', [DeliveryController::class, 'deliver'])->name('projects.deliver');

    // Avaliações
    Route::get('/projetos/{project}/avaliar', [ReviewController::class, 'create'])->name('projects.reviews.create');
    Route::post('/projetos/{project}/avaliar', [ReviewController::class, 'store'])->name('projects.reviews.store');

    // Disputas
    Route::get('/disputas', [DisputeController::class, 'index'])->name('disputes.index');
    Route::get('/projetos/{project}/disputa', [DisputeController::class, 'create'])->name('projects.disputes.create');
    Route::post('/projetos/{project}/disputa', [DisputeController::class, 'store'])->name('projects.disputes.store');
    Route::get('/disputas/{dispute}', [DisputeController::class, 'show'])->name('disputes.show');

    // Propostas
    Route::get('/projetos/{project}/propostas', [ProposalController::class, 'index'])->name('proposals.index');
    Route::get('/projetos/{project}/propostas/enviar', [ProposalController::class, 'create'])->name('proposals.create');
    Route::post('/projetos/{project}/propostas', [ProposalController::class, 'store'])->name('proposals.store');
    Route::post('/projetos/{project}/propostas/{proposal}/aceitar', [ProposalController::class, 'accept'])->name('proposals.accept');
    Route::post('/projetos/{project}/propostas/{proposal}/rejeitar', [ProposalController::class, 'reject'])->name('proposals.reject');

    // Pagamentos
    Route::get('/pagamentos/processar/{project}', [PaymentController::class, 'process'])->name('payments.process');
    Route::post('/pagamentos/processar/{project}', [PaymentController::class, 'process'])->name('payments.process.post');
    Route::get('/pagamentos/boleto/{payment}', [PaymentController::class, 'boleto'])->name('payments.boleto');
    Route::post('/pagamentos/liberar/{payment}', [PaymentController::class, 'release'])->name('payments.release');

    // Saques (freelancer)
    Route::get('/saques', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/saques', [WithdrawalController::class, 'store'])->name('withdrawals.store');

    // Carteira / extrato financeiro
    Route::get('/carteira', [WalletController::class, 'index'])->name('wallet.index');
    Route::get('/carteira/exportar', [WalletController::class, 'export'])->name('wallet.export');

    // Portfólio (freelancer)
    Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio.index');
    Route::post('/portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
    Route::delete('/portfolio/{item}', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');

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

    // Admin: Categorias
    Route::middleware(['can:gerenciar planos'])->prefix('admin/categorias')->name('admin.categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'adminIndex'])->name('index');
        Route::get('/criar', [CategoryController::class, 'adminCreate'])->name('create');
        Route::post('/', [CategoryController::class, 'adminStore'])->name('store');
        Route::get('/{category}/editar', [CategoryController::class, 'adminEdit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'adminUpdate'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'adminDestroy'])->name('destroy');
    });

    // Admin: Habilidades
    Route::middleware(['can:gerenciar planos'])->prefix('admin/habilidades')->name('admin.skills.')->group(function () {
        Route::get('/', [SkillController::class, 'adminIndex'])->name('index');
        Route::get('/criar', [SkillController::class, 'adminCreate'])->name('create');
        Route::post('/', [SkillController::class, 'adminStore'])->name('store');
        Route::delete('/{skill}', [SkillController::class, 'adminDestroy'])->name('destroy');
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
        Route::post('/usuarios/{user}/status', [AdminDashboardController::class, 'updateUserStatus'])->name('users.status');
        Route::get('/pagamentos', [AdminDashboardController::class, 'payments'])->name('payments');
        Route::get('/pagamentos/{payment}', [PaymentController::class, 'adminShow'])->name('payments.show');
        Route::get('/projetos', [AdminDashboardController::class, 'projects'])->name('projects');
        Route::post('/projetos/{project}/aprovar', [ProjectController::class, 'approve'])->name('projects.approve');
        Route::post('/projetos/{project}/reprovar', [ProjectController::class, 'reject'])->name('projects.reject');
        Route::get('/disputas', [AdminDashboardController::class, 'disputes'])->name('disputes');
        Route::post('/disputas/{dispute}/resolver', [DisputeController::class, 'resolve'])->name('disputes.resolve');
        Route::get('/saques', [WithdrawalController::class, 'adminIndex'])->name('withdrawals.index');
        Route::post('/saques/{withdrawal}/pagar', [WithdrawalController::class, 'adminPay'])->name('withdrawals.pay');
        Route::post('/saques/{withdrawal}/cancelar', [WithdrawalController::class, 'adminCancel'])->name('withdrawals.cancel');
    });

    // Logs de Auditoria (apenas admin)
    Route::middleware(['can:gerenciar planos'])->prefix('admin/logs')->name('admin.logs.')->group(function () {
        Route::get('/', [LogController::class, 'index'])->name('index');
        Route::get('/erros', [LogController::class, 'errors'])->name('errors');
        Route::get('/exportar', [LogController::class, 'exportCsv'])->name('export');
    });
});
