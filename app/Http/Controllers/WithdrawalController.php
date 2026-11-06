<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Withdrawal;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Lista saques do freelancer.
     */
    public function index()
    {
        $user = Auth::user();

        if (! $user->isFreelancer() && ! $user->isAdmin()) {
            abort(403, 'Apenas freelancers podem solicitar saque.');
        }

        $available = $user->availableBalance();
        $summary = [
            'pending' => (float) $user->withdrawals()->whereIn('status', ['solicitado', 'processando'])->sum('net_amount'),
            'paid' => (float) $user->withdrawals()->where('status', 'pago')->sum('net_amount'),
            'days' => $this->withdrawalDays($user),
        ];
        $withdrawals = $user->withdrawals()->orderByDesc('created_at')->paginate(20)->withQueryString();

        return view('withdrawals.index', compact('available', 'withdrawals', 'summary'));
    }

    /**
     * Freelancer cancela o próprio saque ainda pendente.
     */
    public function cancel(Withdrawal $withdrawal)
    {
        $user = Auth::user();

        if ($withdrawal->freelancer_id !== $user->id) {
            abort(403);
        }

        if (! in_array($withdrawal->status, ['solicitado', 'processando'], true)) {
            return back()->with('error', 'Apenas saques pendentes podem ser cancelados.');
        }

        $withdrawal->update(['status' => 'cancelado']);

        return back()->with('success', 'Saque cancelado. O valor voltou para seu saldo disponível.');
    }

    /**
     * Solicita saque via Pix.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->isFreelancer()) {
            abort(403, 'Apenas freelancers podem solicitar saque.');
        }

        $available = $user->availableBalance();

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10', 'max:'.$available],
            'pix_key' => ['required', 'string', 'max:100'],
        ], [
            'amount.required' => 'Informe o valor do saque.',
            'amount.min' => 'O valor mínimo é R$ 10,00.',
            'amount.max' => 'O valor excede seu saldo disponível. Disponível: R$ '.number_format($available, 2, ',', '.').'.',
            'pix_key.required' => 'Informe sua chave Pix.',
        ]);

        if ($validated['amount'] > $available) {
            return back()->with('error', 'Saldo insuficiente. Disponível: R$ '.number_format($available, 2, ',', '.'));
        }

        $amount = (float) $validated['amount'];
        $fee = $amount * 0.02; // Saque Pix 2%

        Withdrawal::create([
            'freelancer_id' => $user->id,
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $amount - $fee,
            'pix_key' => $validated['pix_key'],
            'status' => 'solicitado',
        ]);

        return redirect()->route('withdrawals.index')
            ->with('success', 'Saque solicitado! Processamento em até '.($this->withdrawalDays($user)).' dia(s) úteis.');
    }

    /**
     * Admin lista saques (filtro por status e busca).
     */
    public function adminIndex(Request $request)
    {
        $query = Withdrawal::with('freelancer')->orderByDesc('created_at');

        $summary = [
            'pending_count' => (clone $query)->whereIn('status', ['solicitado', 'processando'])->count(),
            'pending_amount' => (float) (clone $query)->whereIn('status', ['solicitado', 'processando'])->sum('net_amount'),
            'paid_amount' => (float) (clone $query)->where('status', 'pago')->sum('net_amount'),
        ];

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('freelancer', fn ($u) => $u->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'))
                    ->orWhere('pix_key', 'like', '%'.$search.'%');
            });
        }

        $withdrawals = $query->paginate(20)->withQueryString();

        return view('admin.withdrawals.index', compact('withdrawals', 'summary'));
    }

    /**
     * Admin marca como pago.
     */
    public function adminPay(Withdrawal $withdrawal)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($withdrawal->status !== 'solicitado' && $withdrawal->status !== 'processando') {
            return back()->with('error', 'Este saque não pode ser pago no estado atual.');
        }

        $withdrawal->update(['status' => 'pago', 'paid_at' => now()]);

        $this->notifications->notify(
            $withdrawal->freelancer,
            'saque_pago',
            'Saque pago',
            'Seu saque de R$ '.number_format((float) $withdrawal->net_amount, 2, ',', '.').' foi pago via Pix.',
            []
        );

        return back()->with('success', 'Saque marcado como pago.');
    }

    /**
     * Admin cancela saque.
     */
    public function adminCancel(Withdrawal $withdrawal)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($withdrawal->status !== 'solicitado' && $withdrawal->status !== 'processando') {
            return back()->with('error', 'Este saque não pode ser cancelado no estado atual.');
        }

        $withdrawal->update(['status' => 'cancelado']);

        $this->notifications->notify(
            $withdrawal->freelancer,
            'saque_cancelado',
            'Saque cancelado',
            'Seu saque de R$ '.number_format((float) $withdrawal->net_amount, 2, ',', '.').' foi cancelado pela moderação. O valor voltou para seu saldo.',
            []
        );

        return back()->with('success', 'Saque cancelado.');
    }

    private function withdrawalDays(User $user): int
    {
        $sub = $user->subscriptions()->where('status', 'ativo')->where('ends_at', '>', now())->first();

        return (int) ($sub?->plan->withdrawal_days ?? 4);
    }
}
