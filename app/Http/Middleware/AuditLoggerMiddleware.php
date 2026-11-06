<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AuditLoggerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Apenas logar ações de mutação (POST, PUT, PATCH, DELETE) de usuários autenticados
        if (auth()->check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logAction($request, $response);
        }

        return $response;
    }

    /**
     * Registra a ação no log de auditoria.
     */
    protected function logAction(Request $request, Response $response): void
    {
        try {
            $route = $request->route();
            if (! $route) {
                return;
            }

            $action = $this->getActionFromMethod($request->method());
            $modelType = $this->getModelTypeFromRoute($route);
            $modelId = $route->parameter('id') ?? $route->parameter('project') ?? $route->parameter('payment') ?? null;

            // Extrair valores antigos e novos do request
            $oldValues = null;
            $newValues = $request->all();

            // Remover dados sensíveis
            unset($newValues['password']);
            unset($newValues['password_confirmation']);
            unset($newValues['_token']);
            unset($newValues['_method']);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'model_type' => $modelType,
                'model_id' => $modelId ? (int) $modelId : null,
                'old_values' => $oldValues,
                'new_values' => $newValues,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Exception $e) {
            // Não falhar a requisição por causa do log de auditoria
            Log::error('Erro no AuditLoggerMiddleware: '.$e->getMessage());
        }
    }

    /**
     * Retorna a ação baseado no método HTTP.
     */
    protected function getActionFromMethod(string $method): string
    {
        return match ($method) {
            'POST' => 'criar',
            'PUT', 'PATCH' => 'editar',
            'DELETE' => 'excluir',
            default => 'desconhecido',
        };
    }

    /**
     * Retorna o tipo do modelo baseado na rota.
     */
    protected function getModelTypeFromRoute($route): string
    {
        $uri = $route->uri();

        if (str_contains($uri, 'usuarios') || str_contains($uri, 'users')) {
            return 'User';
        }
        if (str_contains($uri, 'projetos') || str_contains($uri, 'projects')) {
            return 'Project';
        }
        if (str_contains($uri, 'pagamentos') || str_contains($uri, 'payments')) {
            return 'Payment';
        }
        if (str_contains($uri, 'planos') || str_contains($uri, 'plans')) {
            return 'Plan';
        }
        if (str_contains($uri, 'propostas') || str_contains($uri, 'proposals')) {
            return 'Proposal';
        }
        if (str_contains($uri, 'disputas') || str_contains($uri, 'disputes')) {
            return 'Dispute';
        }

        return 'Unknown';
    }
}
