<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Proposal;
use Illuminate\Http\Request;

class ProposalController extends Controller
{
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

        if ($project->proposals()->where('freelancer_id', $request->user()->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Você já enviou uma proposta para este projeto',
            ], 400);
        }

        $offer = $request->validated('offer');
        $platformFee = $offer * 0.15;
        $finalOffer = $offer + $platformFee;

        $proposal = Proposal::create([
            'project_id' => $project->id,
            'freelancer_id' => $request->user()->id,
            'offer' => $offer,
            'final_offer' => $finalOffer,
            'estimated_days' => $request->validated('estimated_days'),
            'details' => $request->validated('details'),
            'status' => 'pendente',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proposta enviada com sucesso',
            'data' => $proposal->load('freelancer.profile'),
        ], 201);
    }

    /**
     * Validação das propostas.
     */
    protected function validated(array $keys = []): array
    {
        return request()->validate([
            'offer' => ['required', 'numeric', 'min:1'],
            'estimated_days' => ['required', 'integer', 'min:1', 'max:365'],
            'details' => ['required', 'string', 'min:50'],
        ]);
    }
}
