<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();
    }

    /**
     * Define os limitadores de requisições da aplicação.
     */
    protected function configureRateLimiting(): void
    {
        // API autenticada: 60 requisições por minuto por usuário (ou IP quando anônimo).
        RateLimiter::for('api', fn (Request $request) => Limit::perMinute(60)
            ->by($request->user()?->id ?: $request->ip()));

        // Endpoints de autenticação da API: 10 tentativas por minuto por IP.
        RateLimiter::for('api-auth', fn (Request $request) => Limit::perMinute(10)
            ->by($request->ip()));

        // Cadastro público: evita criação em massa de contas por um mesmo IP.
        RateLimiter::for('register', fn (Request $request) => Limit::perMinute(5)
            ->by($request->ip()));

        // Recuperação de senha: 3 pedidos por minuto por e-mail/IP.
        RateLimiter::for('password-reset', fn (Request $request) => Limit::perMinute(3)
            ->by(mb_strtolower((string) $request->input('email')).'|'.$request->ip()));
    }
}
