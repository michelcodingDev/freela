<?php

namespace App\Models;

use Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Relates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'type', 'title', 'message', 'data', 'is_read', 'read_at'])]
class Notification extends Model
{
    /** @use HasFactory<NotificationFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    #[Relates]
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Ícone Lucide exibido para o tipo da notificação.
     */
    public function icon(): string
    {
        return match ($this->type) {
            'proposta_recebida' => 'file-text',
            'proposta_aceita' => 'check-circle',
            'proposta_rejeitada' => 'x-circle',
            'proposta_retirada' => 'undo-2',
            'mensagem_recebida' => 'message-circle',
            'convite_projeto' => 'mail',
            'entrega_realizada' => 'package-check',
            'projeto_iniciado' => 'play-circle',
            'disputa_aberta' => 'shield-alert',
            'avaliacao_recebida' => 'star',
            'saque_pago', 'saque_solicitado' => 'wallet',
            'pagamento_liberado', 'pagamento_recebido' => 'banknote',
            default => 'bell',
        };
    }

    /**
     * Classes de cor do chip do ícone.
     */
    public function iconClasses(): string
    {
        return match ($this->type) {
            'proposta_recebida' => 'bg-blue-100 text-blue-600',
            'proposta_aceita', 'projeto_iniciado', 'saque_pago', 'pagamento_liberado', 'pagamento_recebido' => 'bg-green-100 text-green-600',
            'proposta_rejeitada', 'disputa_aberta' => 'bg-red-100 text-red-600',
            'proposta_retirada' => 'bg-gray-100 text-gray-600',
            'mensagem_recebida' => 'bg-indigo-100 text-indigo-600',
            'convite_projeto' => 'bg-cyan-100 text-cyan-600',
            'entrega_realizada' => 'bg-purple-100 text-purple-600',
            'avaliacao_recebida' => 'bg-amber-100 text-amber-600',
            'saque_solicitado' => 'bg-emerald-100 text-emerald-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    /**
     * URL de destino da notificação, quando há contexto (ex.: projeto).
     */
    public function url(): ?string
    {
        $projectId = $this->data['project_id'] ?? null;

        return match ($this->type) {
            'proposta_recebida' => $projectId ? route('proposals.index', $projectId) : route('dashboard'),
            'proposta_aceita', 'proposta_rejeitada', 'proposta_retirada' => $projectId ? route('projects.show', $projectId) : route('proposals.mine'),
            'mensagem_recebida' => $projectId ? route('projects.messages.index', $projectId) : route('dashboard'),
            'convite_projeto' => route('invites.index'),
            'entrega_realizada', 'projeto_iniciado' => $projectId ? route('projects.show', $projectId) : route('dashboard'),
            'disputa_aberta' => route('disputes.index'),
            'avaliacao_recebida' => route('freelancers.show', $this->user_id),
            'saque_pago', 'saque_solicitado' => route('withdrawals.index'),
            'pagamento_liberado', 'pagamento_recebido' => route('wallet.index'),
            default => $projectId ? route('projects.show', $projectId) : route('notifications.index'),
        };
    }
}
