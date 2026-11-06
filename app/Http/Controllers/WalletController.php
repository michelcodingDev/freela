<?php

namespace App\Http\Controllers;

use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function __construct(private WalletService $wallet) {}

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

        $summary = $this->wallet->summary($user);
        $filters = $this->filters();
        $allTransactions = $this->wallet->transactions($user, $filters);
        $page = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;

        $paginated = new LengthAwarePaginator(
            $allTransactions->forPage($page, $perPage)->values(),
            $allTransactions->count(),
            $perPage,
            $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => request()->query()]
        );

        return view('wallet.index', [
            'user' => $user,
            'summary' => $summary,
            'transactions' => $paginated,
            'totalTransactions' => $allTransactions->count(),
            'filters' => $filters,
        ]);
    }

    /**
     * Normaliza os filtros do extrato a partir da query string.
     *
     * @return array{direction: string, status: string, period: string}
     */
    protected function filters(): array
    {
        $direction = request('direction', 'all');
        $status = request('status', 'all');
        $period = request('period', 'all');

        return [
            'direction' => in_array($direction, ['credito', 'debito', 'all'], true) ? $direction : 'all',
            'status' => $status === '' ? 'all' : $status,
            'period' => in_array($period, ['all', '30d', '90d', '12m'], true) ? $period : 'all',
        ];
    }

    /**
     * Exporta o extrato do usuário em CSV.
     */
    public function export(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            abort(403, 'Use a exportação de pagamentos do admin.');
        }

        $transactions = $this->wallet->transactions($user, $this->filters());

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
}
