<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Freelancer inicia a execução (após custódia).
     */
    public function start(Project $project)
    {
        $this->authorizeHiredFreelancer($project);

        if (! $project->canStartWork()) {
            return back()->with('error', 'Este projeto ainda não pode ser iniciado. Aguarde o pagamento em custódia.');
        }

        $project->update(['status' => 'em_andamento']);

        $this->notifications->notify(
            $project->client,
            'projeto_iniciado',
            'Projeto em andamento',
            "O freelancer iniciou o projeto \"{$project->title}\".",
            ['project_id' => $project->id]
        );

        return back()->with('success', 'Projeto iniciado! Bom trabalho.');
    }

    /**
     * Freelancer entrega o projeto (aguardando liberação).
     */
    public function deliver(Project $project)
    {
        $this->authorizeHiredFreelancer($project);

        if (! $project->canBeDelivered()) {
            return back()->with('error', 'Este projeto não está em andamento.');
        }

        $project->update(['status' => 'aguardando_liberacao']);

        $this->notifications->notify(
            $project->client,
            'entrega_realizada',
            'Entrega realizada',
            "O freelancer entregou o projeto \"{$project->title}\". Revise e libere o pagamento.",
            ['project_id' => $project->id]
        );

        return back()->with('success', 'Entrega enviada! Aguarde a liberação do cliente.');
    }

    private function authorizeHiredFreelancer(Project $project): void
    {
        $proposal = $project->acceptedProposal;
        if (! $proposal || $proposal->freelancer_id !== Auth::id()) {
            abort(403, 'Apenas o freelancer contratado pode realizar esta ação.');
        }
    }
}
