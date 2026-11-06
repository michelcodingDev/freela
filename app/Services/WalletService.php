<?php

namespace App\Services;

use App\Models\User;
use App\Support\Mask;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class WalletService
{
    /**
     * Resumo financeiro conforme o tipo de conta.
     *
     * @return array<string, float|int>
     */
    public function summary(User $user): array
    {
        return $user->isFreelancer()
            ? $this->freelancerSummary($user)
            : $this->clientSummary($user);
    }

    /**
     * Extrato unificado (pagamentos e saques) em ordem decrescente.
     *
     * Filtros suportados: direction (credito|debito), status, period (all|30d|90d|12m).
     *
     * @param  array<string, mixed>  $filters
     * @return Collection<int, array{date: Carbon, direction: string, description: string, amount: float, status: string, status_label: string, url: ?string}>
     */
    public function transactions(User $user, array $filters = []): Collection
    {
        $transactions = collect();

        $payments = $user->isFreelancer()
            ? $user->paymentsAsFreelancer()->with('project')->get()
            : $user->paymentsAsClient()->with('project')->get();

        foreach ($payments as $payment) {
            $transactions->push([
                'date' => $payment->created_at,
                'direction' => $user->isFreelancer() ? 'credito' : 'debito',
                'description' => ($user->isFreelancer() ? 'Recebimento: ' : 'Pagamento: ').($payment->project?->title ?? 'Projeto'),
                'amount' => (float) ($user->isFreelancer() ? $payment->net_amount : $payment->amount),
                'status' => $payment->status,
                'status_label' => $this->paymentStatusLabel($payment->status),
                'url' => $payment->project_id ? route('projects.show', $payment->project_id) : null,
            ]);
        }

        if ($user->isFreelancer()) {
            foreach ($user->withdrawals()->get() as $withdrawal) {
                $transactions->push([
                    'date' => $withdrawal->created_at,
                    'direction' => 'debito',
                    'description' => 'Saque Pix para '.Mask::pixKey($withdrawal->pix_key),
                    'amount' => (float) $withdrawal->net_amount,
                    'status' => $withdrawal->status,
                    'status_label' => $this->withdrawalStatusLabel($withdrawal->status),
                    'url' => route('withdrawals.index'),
                ]);
            }
        }

        return $transactions->sortByDesc('date')->values()
            ->filter(function (array $transaction) use ($filters) {
                if (($filters['direction'] ?? 'all') !== 'all' && $transaction['direction'] !== $filters['direction']) {
                    return false;
                }

                if (($filters['status'] ?? 'all') !== 'all' && $transaction['status'] !== $filters['status']) {
                    return false;
                }

                $period = $filters['period'] ?? 'all';
                if (in_array($period, ['30d', '90d', '12m'], true)) {
                    $days = match ($period) {
                        '30d' => 30,
                        '90d' => 90,
                        default => 365,
                    };

                    if ($transaction['date']->lt(now()->subDays($days))) {
                        return false;
                    }
                }

                return true;
            })
            ->values();
    }

    /**
     * @return array<string, float|int>
     */
    private function freelancerSummary(User $user): array
    {
        $released = $user->paymentsAsFreelancer()->where('status', 'liberado');

        return [
            'available' => $user->availableBalance(),
            'locked' => $user->lockedBalance(),
            'gross_released' => (float) (clone $released)->sum('amount'),
            'platform_fees' => (float) (clone $released)->sum('platform_fee'),
            'total_earned' => (float) (clone $released)->sum('net_amount'),
            'withdrawals_paid' => (float) $user->withdrawals()->where('status', 'pago')->sum('net_amount'),
            'withdrawals_pending' => (float) $user->withdrawals()->whereIn('status', ['solicitado', 'processando'])->sum('net_amount'),
            'projects_paid' => (clone $released)->distinct('project_id')->count('project_id'),
        ];
    }

    /**
     * @return array<string, float|int>
     */
    private function clientSummary(User $user): array
    {
        $payments = $user->paymentsAsClient();

        return [
            'in_escrow' => (float) (clone $payments)->where('status', 'custodia')->sum('amount'),
            'total_paid' => (float) (clone $payments)->whereIn('status', ['custodia', 'liberado'])->sum('amount'),
            'released' => (float) (clone $payments)->where('status', 'liberado')->sum('amount'),
            'refunded' => (float) (clone $payments)->where('status', 'estornado')->sum('amount'),
            'active_payments' => (clone $payments)->whereIn('status', ['pendente', 'custodia'])->count(),
        ];
    }

    private function paymentStatusLabel(string $status): string
    {
        return match ($status) {
            'pendente' => 'Pendente',
            'custodia' => 'Em custódia',
            'liberado' => 'Liberado',
            'estornado' => 'Estornado',
            default => ucfirst($status),
        };
    }

    private function withdrawalStatusLabel(string $status): string
    {
        return match ($status) {
            'solicitado' => 'Solicitado',
            'processando' => 'Processando',
            'pago' => 'Pago',
            'cancelado' => 'Cancelado',
            default => ucfirst($status),
        };
    }
}
