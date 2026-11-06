<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlanRequest;
use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PlanController extends Controller
{
    /**
     * Lista todos os planos (admin).
     */
    public function index()
    {
        Gate::authorize('gerenciar planos');

        $plans = Plan::orderBy('sort_order')->get();

        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Exibe formulário para criar plano.
     */
    public function create()
    {
        Gate::authorize('gerenciar planos');

        return view('admin.plans.create');
    }

    /**
     * Armazena novo plano.
     */
    public function store(PlanRequest $request)
    {
        Gate::authorize('gerenciar planos');

        $validated = $request->validated();

        $plan = Plan::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price_monthly' => $validated['price_monthly'],
            'commission_percentage' => $validated['commission_percentage'],
            'min_commission' => $validated['min_commission'],
            'proposals_per_month' => $validated['proposals_per_month'],
            'max_portfolio_items' => $validated['max_portfolio_items'],
            'withdrawal_days' => $validated['withdrawal_days'],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        // Criar funcionalidades do plano
        if (isset($validated['features'])) {
            foreach ($validated['features'] as $feature) {
                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'name' => $feature['name'],
                    'description' => $feature['description'],
                    'is_included' => $feature['is_included'] ?? true,
                ]);
            }
        }

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano criado com sucesso!');
    }

    /**
     * Exibe detalhes do plano.
     */
    public function show(Plan $plan)
    {
        Gate::authorize('gerenciar planos');

        $plan->load('features');

        return view('admin.plans.show', compact('plan'));
    }

    /**
     * Exibe formulário para editar plano.
     */
    public function edit(Plan $plan)
    {
        Gate::authorize('gerenciar planos');

        $plan->load('features');

        return view('admin.plans.edit', compact('plan'));
    }

    /**
     * Atualiza o plano.
     */
    public function update(PlanRequest $request, Plan $plan)
    {
        Gate::authorize('gerenciar planos');

        $validated = $request->validated();

        $plan->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'],
            'price_monthly' => $validated['price_monthly'],
            'commission_percentage' => $validated['commission_percentage'],
            'min_commission' => $validated['min_commission'],
            'proposals_per_month' => $validated['proposals_per_month'],
            'max_portfolio_items' => $validated['max_portfolio_items'],
            'withdrawal_days' => $validated['withdrawal_days'],
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        // Sincroniza funcionalidades: recria a partir do formulário.
        $plan->features()->delete();
        if (isset($validated['features'])) {
            foreach ($validated['features'] as $feature) {
                PlanFeature::create([
                    'plan_id' => $plan->id,
                    'name' => $feature['name'],
                    'description' => $feature['description'],
                    'is_included' => $feature['is_included'] ?? true,
                ]);
            }
        }

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano atualizado com sucesso!');
    }

    /**
     * Exclui o plano.
     */
    public function destroy(Plan $plan)
    {
        Gate::authorize('gerenciar planos');

        // Verificar se há assinaturas ativas
        if ($plan->subscriptions()->where('status', 'ativo')->exists()) {
            return back()->with('error', 'Não é possível excluir um plano com assinaturas ativas.');
        }

        $plan->delete();

        return redirect()->route('admin.plans.index')
            ->with('success', 'Plano excluído com sucesso!');
    }

    /**
     * Ativa/desativa o plano.
     */
    public function toggleStatus(Request $request, Plan $plan)
    {
        Gate::authorize('gerenciar planos');

        $plan->update(['is_active' => $request->boolean('is_active')]);

        return response()->json([
            'success' => true,
            'is_active' => $plan->is_active,
        ]);
    }
}
