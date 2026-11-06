<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\DisputeController;
use App\Http\Controllers\API\FreelancerController;
use App\Http\Controllers\API\InviteController;
use App\Http\Controllers\API\MessageController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\ProjectController;
use App\Http\Controllers\API\ProposalController;
use App\Http\Controllers\API\RankingController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\VerificationController;
use App\Http\Controllers\API\WalletController;
use App\Http\Controllers\API\WithdrawalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rotas públicas de autenticação
Route::prefix('auth')->name('api.auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:api-auth')->name('register');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:api-auth')->name('login');
});

// Rotas protegidas (requer token de API)
Route::middleware(['api.token', 'throttle:api'])->name('api.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/me', [AuthController::class, 'me'])->name('me');

    // Verificação de e-mail (não bloqueante)
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])->middleware('throttle:6,1')->name('verification.send');
    Route::get('/email/verification-status', [VerificationController::class, 'status'])->name('verification.status');

    // Perfil
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/senha', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    // Projetos (rotas literais antes do binding {project})
    Route::get('/projetos', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/projetos/categorias', [ProjectController::class, 'categories'])->name('projects.categories');
    Route::get('/projetos/habilidades', [ProjectController::class, 'skills'])->name('projects.skills');
    Route::get('/projetos/{project}', [ProjectController::class, 'show'])->name('projects.show');

    // Propostas
    Route::get('/projetos/{project}/propostas', [ProposalController::class, 'index'])->name('proposals.index');
    Route::post('/projetos/{project}/propostas', [ProposalController::class, 'store'])->name('proposals.store');
    Route::post('/projetos/{project}/propostas/{proposal}/aceitar', [ProposalController::class, 'accept'])->name('proposals.accept');
    Route::post('/projetos/{project}/propostas/{proposal}/rejeitar', [ProposalController::class, 'reject'])->name('proposals.reject');
    Route::get('/minhas-propostas', [ProposalController::class, 'mine'])->name('proposals.mine');
    Route::post('/propostas/{proposal}/retirar', [ProposalController::class, 'withdraw'])->name('proposals.withdraw');

    // Carteira
    Route::get('/carteira', [WalletController::class, 'index'])->name('wallet.index');

    // Pagamentos
    Route::post('/projetos/{project}/pagamentos', [PaymentController::class, 'create'])->name('payments.create');
    Route::get('/pagamentos', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/pagamentos/{payment}/liberar', [PaymentController::class, 'release'])->name('payments.release');

    // Chat
    Route::get('/projetos/{project}/mensagens', [MessageController::class, 'index'])->name('projects.messages.index');
    Route::post('/projetos/{project}/mensagens', [MessageController::class, 'store'])->name('projects.messages.store');

    // Avaliações
    Route::post('/projetos/{project}/avaliar', [ReviewController::class, 'store'])->name('projects.reviews.store');

    // Saques
    Route::get('/saques', [WithdrawalController::class, 'index'])->name('withdrawals.index');
    Route::post('/saques', [WithdrawalController::class, 'store'])->name('withdrawals.store');
    Route::post('/saques/{withdrawal}/cancelar', [WithdrawalController::class, 'cancel'])->name('withdrawals.cancel');

    // Disputas
    Route::get('/disputas', [DisputeController::class, 'index'])->name('disputes.index');
    Route::post('/projetos/{project}/disputa', [DisputeController::class, 'store'])->name('projects.disputes.store');

    // Notificações
    Route::get('/notificacoes', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificacoes/ler-todas', [NotificationController::class, 'readAll'])->name('notifications.readAll');
    Route::post('/notificacoes/{notification}/ler', [NotificationController::class, 'read'])->name('notifications.read');

    // Freelancers
    Route::get('/freelancers', [FreelancerController::class, 'index'])->name('freelancers.index');
    Route::get('/freelancers/{freelancer}', [FreelancerController::class, 'show'])->name('freelancers.show');
    Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

    // Convites
    Route::get('/convites', [InviteController::class, 'index'])->name('invites.index');
    Route::post('/projetos/{project}/convidar', [InviteController::class, 'store'])->name('projects.invites.store');
    Route::post('/convites/{invite}/aceitar', [InviteController::class, 'accept'])->name('invites.accept');
    Route::post('/convites/{invite}/recusar', [InviteController::class, 'decline'])->name('invites.decline');
});
