<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Proposal;

class ProposalController extends Controller
{
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
        if ($project->proposals()->where('freelancer_id', auth()->id())->exists()) {
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

        // Calcular oferta final com comissão da plataforma
        $offer = $request->offer;
        $platformFee = $offer * 0.15; // 15% padrão (pode variar por plano)
        $finalOffer = $offer + $platformFee;

        $proposal = Proposal::create([
            'project_id' => $project->id,
            'freelancer_id' => auth()->id(),
            'offer' => $offer,
            'final_offer' => $finalOffer,
            'estimated_days' => $request->estimated_days,
            'details' => $request->details,
            'status' => 'pendente',
        ]);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proposta enviada com sucesso!');
    }

    /**
     * Lista propostas de um projeto (apenas cliente).
     */
    public function index(Project $project)
    {
        // Verificar se o usuário é o cliente do projeto
        if (auth()->id() !== $project->client_id) {
            abort(403, 'Você não tem permissão para ver as propostas deste projeto.');
        }

        $proposals = $project->proposals()
            ->with('freelancer.profile')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('proposals.index', compact('project', 'proposals'));
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

        // Rejeitar outras propostas pendentes
        $project->proposals()->where('status', 'pendente')
            ->where('id', '!=', $proposal->id)
            ->update(['status' => 'rejeitada']);

        // Aceitar a proposta selecionada
        $proposal->update(['status' => 'aceita']);

        // Atualizar status do projeto
        $project->update(['status' => 'aguardando_pagamento']);

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

        $proposal->update(['status' => 'rejeitada']);

        return back()->with('success', 'Proposta rejeitada.');
    }
}
