<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Lista propostas de um projeto (apenas cliente).
     */
    public function index(Request $request, Project $project)
    {
        if ($request->user()->id !== $project->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $proposals = $project->proposals()
            ->with('freelancer.profile')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $proposals,
        ]);
    }

    /**
     * Lista as propostas enviadas pelo freelancer autenticado.
     */
    public function mine(Request $request)
    {
        if (! $request->user()->isFreelancer()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas freelancers possuem propostas enviadas.',
            ], 403);
        }

        $proposals = Proposal::where('freelancer_id', $request->user()->id)
            ->with(['project.category'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $proposals,
        ]);
    }

    /**
     * Envia uma proposta (apenas freelancer).
     */
    public function store(Request $request, Project $project)
    {
        if (! $request->user()->isFreelancer()) {
            return response()->json([
                'success' => false,
                'message' => 'Apenas freelancers podem enviar propostas',
            ], 403);
        }

        if (! $project->canReceiveProposals()) {
            return response()->json([
                'success' => false,
                'message' => 'Projeto não está recebendo propostas',
            ], 403);
        }

        $alreadySent = $project->proposals()
            ->where('freelancer_id', $request->user()->id)
            ->whereIn('status', ['pendente', 'aceita'])
            ->exists();

        if ($alreadySent) {
            return response()->json([
                'success' => false,
                'message' => 'Você já enviou uma proposta para este projeto',
            ], 400);
        }

        $limit = $this->proposalLimitFor($request->user());
        $used = Proposal::where('freelancer_id', $request->user()->id)
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();

        if ($used >= $limit) {
            return response()->json([
                'success' => false,
                'message' => "Limite de {$limit} propostas do plano atingido neste mês.",
            ], 422);
        }

        $validated = $request->validate([
            'offer' => ['required', 'numeric', 'min:1'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:365'],
            'details' => ['required', 'string', 'min:50'],
        ]);

        $offer = (float) $validated['offer'];

        $proposal = Proposal::create([
            'project_id' => $project->id,
            'freelancer_id' => $request->user()->id,
            'offer' => $offer,
            'final_offer' => $offer,
            'estimated_days' => $validated['estimated_days'],
            'details' => $validated['details'],
            'status' => 'pendente',
        ]);

        $this->notifications->notify(
            $project->client,
            'proposta_recebida',
            'Nova proposta recebida',
            'Você recebeu uma proposta de R$ '.number_format($offer, 2, ',', '.')." no projeto \"{$project->title}\".",
            ['project_id' => $project->id]
        );

        return response()->json([
            'success' => true,
            'message' => 'Proposta enviada com sucesso',
            'data' => $proposal->load('freelancer.profile'),
        ], 201);
    }

    /**
     * Aceita uma proposta (apenas cliente dono do projeto).
     */
    public function accept(Request $request, Project $project, Proposal $proposal)
    {
        if ($request->user()->id !== $project->client_id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        if ($proposal->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Proposta não encontrada'], 404);
        }

        if ($project->status !== 'aguardando_propostas') {
            return response()->json(['success' => false, 'message' => 'Este projeto não está mais recebendo propostas.'], 422);
        }

        if ($proposal->status !== 'pendente') {
            return response()->json(['success' => false, 'message' => 'Esta proposta não pode mais ser aceita.'], 422);
        }

        $project->proposals()->where('status', 'pendente')
            ->where('id', '!=', $proposal->id)
            ->update(['status' => 'rejeitada']);

        $proposal->update(['status' => 'aceita']);
        $project->update(['status' => 'aguardando_pagamento']);

        $this->notifications->notify(
            $proposal->freelancer,
            'proposta_aceita',
            'Proposta aceita!',
            "Sua proposta no projeto \"{$project->title}\" foi aceita. Aguarde o pagamento em custódia.",
            ['project_id' => $project->id]
        );

        return response()->json(['success' => true, 'data' => $proposal]);
    }

    /**
     * Rejeita uma proposta (apenas cliente dono do projeto).
     */
    public function reject(Request $request, Project $project, Proposal $proposal)
    {
        if ($request->user()->id !== $project->client_id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        if ($proposal->project_id !== $project->id) {
            return response()->json(['success' => false, 'message' => 'Proposta não encontrada'], 404);
        }

        if ($proposal->status !== 'pendente') {
            return response()->json(['success' => false, 'message' => 'Apenas propostas pendentes podem ser rejeitadas.'], 422);
        }

        $proposal->update(['status' => 'rejeitada']);

        $this->notifications->notify(
            $proposal->freelancer,
            'proposta_rejeitada',
            'Proposta não selecionada',
            "Sua proposta no projeto \"{$project->title}\" não foi selecionada.",
            ['project_id' => $project->id]
        );

        return response()->json(['success' => true, 'data' => $proposal]);
    }

    /**
     * Freelancer retira uma proposta pendente.
     */
    public function withdraw(Request $request, Proposal $proposal)
    {
        if ($proposal->freelancer_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        if ($proposal->status !== 'pendente') {
            return response()->json(['success' => false, 'message' => 'Apenas propostas pendentes podem ser retiradas.'], 422);
        }

        $project = $proposal->project;

        if ($project->status !== 'aguardando_propostas') {
            return response()->json(['success' => false, 'message' => 'O projeto não está mais recebendo propostas.'], 422);
        }

        $proposal->update(['status' => 'retirada']);

        $this->notifications->notify(
            $project->client,
            'proposta_retirada',
            'Proposta retirada',
            "{$request->user()->name} retirou a proposta do projeto \"{$project->title}\".",
            ['project_id' => $project->id]
        );

        return response()->json(['success' => true, 'data' => $proposal]);
    }

    private function proposalLimitFor(User $user): int
    {
        $sub = $user->subscriptions()->where('status', 'ativo')->where('ends_at', '>', now())->first();

        return (int) ($sub?->plan->proposals_per_month ?? 10);
    }
}
