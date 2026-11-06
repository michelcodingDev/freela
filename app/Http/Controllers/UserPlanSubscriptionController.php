<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\UserPlanSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class UserPlanSubscriptionController extends Controller
{
    /**
     * Exibe a página de escolha de planos.
     */
    public function index()
    {
        $plans = Plan::active()->orderBy('sort_order')->get();
        $currentSubscription = Auth::user()->subscriptions()->where('status', 'ativo')
            ->where('ends_at', '>', now())
            ->first();

        return view('subscriptions.index', compact('plans', 'currentSubscription'));
    }

    /**
     * Assina um plano.
     */
    public function store(Request $request, Plan $plan)
    {
        $user = Auth::user();

        // Cancelar assinatura ativa anterior
        $user->subscriptions()->where('status', 'ativo')->update(['status' => 'cancelado']);

        // Calcular data de fim (30 dias)
        $endsAt = now()->addDays(30);

        // Criar nova assinatura
        UserPlanSubscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => 'ativo',
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'price_paid' => $plan->price_monthly,
            'payment_method' => $request->payment_method ?? 'pix',
        ]);

        return redirect()->route('dashboard')
            ->with('success', "Parabéns! Você assinou o plano {$plan->name}.");
    }

    /**
     * Cancela a assinatura atual.
     */
    public function cancel()
    {
        $user = Auth::user();
        $subscription = $user->subscriptions()->where('status', 'ativo')
            ->where('ends_at', '>', now())
            ->first();

        if ($subscription) {
            $subscription->update(['status' => 'cancelado']);
        }

        return back()->with('success', 'Assinatura cancelada com sucesso.');
    }

    /**
     * Admin: lista todas as assinaturas.
     */
    public function adminIndex()
    {
        Gate::authorize('gerenciar planos');

        $subscriptions = UserPlanSubscription::with(['user', 'plan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}
