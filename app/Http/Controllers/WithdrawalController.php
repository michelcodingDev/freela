<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
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
        $withdrawals = $user->withdrawals()->orderByDesc('created_at')->paginate(20);

        return view('withdrawals.index', compact('available', 'withdrawals'));
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

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10'],
            'pix_key' => ['required', 'string', 'max:100'],
        ], [
            'amount.required' => 'Informe o valor do saque.',
            'amount.min' => 'O valor mínimo é R$ 10,00.',
            'pix_key.required' => 'Informe sua chave Pix.',
        ]);

        $available = $user->availableBalance();

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
     * Admin lista saques.
     */
    public function adminIndex()
    {
        $withdrawals = Withdrawal::with('freelancer')->orderByDesc('created_at')->paginate(20);

        return view('admin.withdrawals.index', compact('withdrawals'));
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

        return back()->with('success', 'Saque cancelado.');
    }

    private function withdrawalDays(User $user): int
    {
        $sub = $user->subscriptions()->where('status', 'ativo')->where('ends_at', '>', now())->first();

        return (int) ($sub?->plan->withdrawal_days ?? 4);
    }
}
