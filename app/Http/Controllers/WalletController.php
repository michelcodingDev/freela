<?php

namespace App\Http\Controllers;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    /**
     * Carteira e extrato financeiro do usuário (freelancer ou cliente).
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.payments');
        }

        if (! $user->isCliente() && ! $user->isFreelancer()) {
            abort(403, 'Carteira indisponível para este tipo de conta.');
        }

        $summary = $user->isFreelancer()
            ? $this->freelancerSummary($user)
            : $this->clientSummary($user);

        $transactions = $this->transactions($user);
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;

        $paginated = new LengthAwarePaginator(
            $transactions->forPage($page, $perPage)->values(),
            $transactions->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return view('wallet.index', [
            'user' => $user,
            'summary' => $summary,
            'transactions' => $paginated,
            'totalTransactions' => $transactions->count(),
        ]);
    }

    /**
     * Exporta o extrato do usuário em CSV.
     */
    public function export()
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            abort(403, 'Use a exportação de pagamentos do admin.');
        }

        $transactions = $this->transactions($user);

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="extrato_'.$user->id.'_'.now()->format('Ymd_His').'.csv"',
        ];

        $callback = function () use ($transactions) {
            $handle = fopen('php://output', 'w');
            // BOM para o Excel reconhecer os acentos.
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Data', 'Tipo', 'Descrição', 'Valor (R$)', 'Status']);

            foreach ($transactions as $transaction) {
                fputcsv($handle, [
                    $transaction['date']->format('d/m/Y H:i'),
                    $transaction['direction'] === 'credito' ? 'Crédito' : 'Débito',
                    $transaction['description'],
                    number_format($transaction['amount'], 2, ',', '.'),
                    $transaction['status_label'],
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * @return array<string, float|int>
     */
    private function freelancerSummary($user): array
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
    private function clientSummary($user): array
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

    /**
     * Monta o extrato unificado (pagamentos e saques) em ordem decrescente.
     *
     * @return Collection<int, array{date: Carbon, direction: string, description: string, amount: float, status: string, status_label: string, url: ?string}>
     */
    private function transactions($user): Collection
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
                    'description' => 'Saque Pix para '.$this->maskPixKey($withdrawal->pix_key),
                    'amount' => (float) $withdrawal->net_amount,
                    'status' => $withdrawal->status,
                    'status_label' => $this->withdrawalStatusLabel($withdrawal->status),
                    'url' => route('withdrawals.index'),
                ]);
            }
        }

        return $transactions->sortByDesc('date')->values();
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

    private function maskPixKey(?string $key): string
    {
        if (! $key) {
            return 'chave não informada';
        }

        if (strlen($key) <= 6) {
            return str_repeat('*', strlen($key));
        }

        return substr($key, 0, 3).str_repeat('*', max(3, strlen($key) - 6)).substr($key, -3);
    }
}
