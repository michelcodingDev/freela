<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProposalRequest;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProposalController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Exibe formulário para criar proposta (apenas freelancers).
     */
    public function create(Project $project)
    {
        // Verificar se o projeto pode receber propostas
        if (! $project->canReceiveProposals()) {
            abort(403, 'Este projeto não está recebendo propostas no momento.');
        }

        // Verificar se o usuário é freelancer
        if (! auth()->user()->isFreelancer()) {
            abort(403, 'Apenas freelancers podem enviar propostas.');
        }

        // Verificar se já enviou proposta para este projeto
        if ($project->proposals()->where('freelancer_id', auth()->id())->whereIn('status', ['pendente', 'aceita'])->exists()) {
            return redirect()->route('projects.show', $project)
                ->with('info', 'Você já enviou uma proposta para este projeto.');
        }

        return view('proposals.create', compact('project'));
    }

    /**
     * Armazena nova proposta.
     */
    public function store(ProposalRequest $request, Project $project)
    {
        if (! $project->canReceiveProposals()) {
            abort(403, 'Este projeto não está recebendo propostas no momento.');
        }

        if (! auth()->user()->isFreelancer()) {
            abort(403, 'Apenas freelancers podem enviar propostas.');
        }

        if ($project->proposals()->where('freelancer_id', auth()->id())->whereIn('status', ['pendente', 'aceita'])->exists()) {
            return redirect()->route('projects.show', $project)
                ->with('info', 'Você já enviou uma proposta para este projeto.');
        }

        // Limite mensal do plano (RF05: Free 10/mês)
        $limit = $this->proposalLimitFor(auth()->user());
        $used = Proposal::where('freelancer_id', auth()->id())
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        if ($used >= $limit) {
            return redirect()->route('projects.show', $project)
                ->with('error', "Você atingiu o limite de {$limit} propostas do seu plano neste mês. Faça upgrade para continuar.");
        }

        // Oferta final = valor pago pelo cliente (a comissão sai do líquido do freelancer via PaymentService)
        $offer = $request->offer;
        $finalOffer = $offer;

        $proposal = Proposal::create([
            'project_id' => $project->id,
            'freelancer_id' => auth()->id(),
            'offer' => $offer,
            'final_offer' => $finalOffer,
            'estimated_days' => $request->estimated_days,
            'details' => $request->details,
            'status' => 'pendente',
        ]);

        $this->notifications->notify(
            $project->client,
            'proposta_recebida',
            'Nova proposta recebida',
            'Você recebeu uma proposta de R$ '.number_format($offer, 2, ',', '.')." no projeto \"{$project->title}\".",
            ['project_id' => $project->id]
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proposta enviada com sucesso!');
    }

    /**
     * Lista propostas de um projeto (apenas cliente).
     *
     * Suporta ordenação (?sort=) e filtro por status (?status=).
     */
    public function index(Request $request, Project $project)
    {
        // Verificar se o usuário é o cliente do projeto
        if (auth()->id() !== $project->client_id) {
            abort(403, 'Você não tem permissão para ver as propostas deste projeto.');
        }

        $sort = $request->get('sort', 'recent');
        if (! in_array($sort, ['recent', 'offer_asc', 'offer_desc', 'days_asc', 'rating_desc'], true)) {
            $sort = 'recent';
        }

        $filter = $request->get('status', 'all');
        if (! in_array($filter, ['all', 'pendente', 'aceita', 'rejeitada', 'retirada'], true)) {
            $filter = 'all';
        }

        $baseQuery = $project->proposals()
            ->with([
                'freelancer' => fn ($q) => $q->with(['profile', 'skills'])
                    ->withAvg(['reviewsReceived as rating_avg' => fn ($qq) => $qq->where('is_public', true)], 'rating')
                    ->withCount(['reviewsReceived' => fn ($qq) => $qq->where('is_public', true)]),
            ]);

        $pending = (clone $baseQuery)->where('status', 'pendente');

        $summary = [
            'total' => (clone $baseQuery)->count(),
            'pending' => (clone $pending)->count(),
            'cheapest' => (clone $pending)->min('offer'),
            'fastest' => (clone $pending)->min('estimated_days'),
        ];

        $query = clone $baseQuery;

        if ($filter !== 'all') {
            $query->where('status', $filter);
        }

        match ($sort) {
            'offer_asc' => $query->orderBy('offer'),
            'offer_desc' => $query->orderByDesc('offer'),
            'days_asc' => $query->orderBy('estimated_days'),
            'rating_desc' => $query->orderByDesc(DB::raw('COALESCE((SELECT AVG(rating) FROM reviews WHERE reviews.to_user_id = proposals.freelancer_id AND reviews.is_public = 1 AND reviews.deleted_at IS NULL), 0)')),
            default => $query->orderBy('created_at', 'desc'),
        };

        $proposals = $query->get();

        return view('proposals.index', compact('project', 'proposals', 'summary', 'sort', 'filter'));
    }

    /**
     * Aceita uma proposta.
     */
    public function accept(Project $project, Proposal $proposal)
    {
        // Verificar se o usuário é o cliente do projeto
        if (auth()->id() !== $project->client_id) {
            abort(403, 'Você não tem permissão para aceitar propostas.');
        }

        // Verificar se a proposta pertence a este projeto
        if ($proposal->project_id !== $project->id) {
            abort(404, 'Proposta não encontrada.');
        }

        if ($project->status !== 'aguardando_propostas') {
            return back()->with('error', 'Este projeto não está mais recebendo propostas.');
        }

        if ($proposal->status !== 'pendente') {
            return back()->with('error', 'Esta proposta não pode mais ser aceita.');
        }

        // Rejeitar outras propostas pendentes
        $project->proposals()->where('status', 'pendente')
            ->where('id', '!=', $proposal->id)
            ->update(['status' => 'rejeitada']);

        // Aceitar a proposta selecionada
        $proposal->update(['status' => 'aceita']);

        // Atualizar status do projeto
        $project->update(['status' => 'aguardando_pagamento']);

        $this->notifications->notify(
            $proposal->freelancer,
            'proposta_aceita',
            'Proposta aceita!',
            "Sua proposta no projeto \"{$project->title}\" foi aceita. Aguarde o pagamento em custódia.",
            ['project_id' => $project->id]
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proposta aceita! Agora realize o pagamento para iniciar o projeto.');
    }

    /**
     * Rejeita uma proposta.
     */
    public function reject(Project $project, Proposal $proposal)
    {
        // Verificar se o usuário é o cliente do projeto
        if (auth()->id() !== $project->client_id) {
            abort(403, 'Você não tem permissão para rejeitar propostas.');
        }

        // Verificar se a proposta pertence a este projeto
        if ($proposal->project_id !== $project->id) {
            abort(404, 'Proposta não encontrada.');
        }

        if ($proposal->status !== 'pendente') {
            return back()->with('error', 'Apenas propostas pendentes podem ser rejeitadas.');
        }

        $proposal->update(['status' => 'rejeitada']);

        $this->notifications->notify(
            $proposal->freelancer,
            'proposta_rejeitada',
            'Proposta não selecionada',
            "Sua proposta no projeto \"{$project->title}\" não foi selecionada.",
            ['project_id' => $project->id]
        );

        return back()->with('success', 'Proposta rejeitada.');
    }

    /**
     * Lista as propostas enviadas pelo freelancer autenticado.
     */
    public function mine()
    {
        if (! auth()->user()->isFreelancer()) {
            abort(403, 'Apenas freelancers possuem propostas enviadas.');
        }

        $proposals = Proposal::where('freelancer_id', auth()->id())
            ->with(['project.category'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('proposals.mine', compact('proposals'));
    }

    /**
     * Freelancer retira uma proposta ainda pendente.
     */
    public function withdraw(Proposal $proposal)
    {
        if ($proposal->freelancer_id !== auth()->id()) {
            abort(403, 'Você não pode retirar esta proposta.');
        }

        if ($proposal->status !== 'pendente') {
            return back()->with('error', 'Apenas propostas pendentes podem ser retiradas.');
        }

        $project = $proposal->project;

        if ($project->status !== 'aguardando_propostas') {
            return back()->with('error', 'O projeto não está mais recebendo propostas.');
        }

        $proposal->update(['status' => 'retirada']);

        $this->notifications->notify(
            $project->client,
            'proposta_retirada',
            'Proposta retirada',
            "{$proposal->freelancer->name} retirou a proposta do projeto \"{$project->title}\".",
            ['project_id' => $project->id]
        );

        return back()->with('success', 'Proposta retirada.');
    }

    private function proposalLimitFor(User $user): int
    {
        $sub = $user->subscriptions()->where('status', 'ativo')->where('ends_at', '>', now())->first();

        return (int) ($sub?->plan->proposals_per_month ?? 10);
    }
}
