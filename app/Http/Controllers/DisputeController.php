<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\Project;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisputeController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Lista disputas do usuário / admin.
     */
    public function index()
    {
        $user = Auth::user();

        $query = Dispute::with(['project', 'client', 'freelancer'])
            ->orderByDesc('created_at');

        if (! $user->isAdmin()) {
            $query->where(fn ($q) => $q->where('client_id', $user->id)->orWhere('freelancer_id', $user->id));
        }

        $disputes = $query->paginate(20);

        return view('disputes.index', compact('disputes'));
    }

    /**
     * Formulário para abrir disputa.
     */
    public function create(Project $project)
    {
        $this->authorizeParticipant($project);

        if (! in_array($project->status, ['em_andamento', 'aguardando_liberacao', 'aguardando_pagamento'], true)) {
            abort(403, 'Disputa disponível apenas para projetos em execução.');
        }

        if ($project->disputes()->where('status', 'aberta')->exists()) {
            return redirect()->route('projects.show', $project)
                ->with('info', 'Já existe uma disputa aberta para este projeto.');
        }

        return view('disputes.create', compact('project'));
    }

    /**
     * Abre disputa e bloqueia o escrow.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorizeParticipant($project);

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:20', 'max:2000'],
            'argument' => ['nullable', 'string', 'max:2000'],
        ], [
            'reason.required' => 'Descreva o motivo da disputa.',
            'reason.min' => 'Descreva o motivo com ao menos 20 caracteres.',
        ]);

        $proposal = $project->acceptedProposal;
        if (! $proposal) {
            return back()->with('error', 'Este projeto não tem freelancer contratado.');
        }

        $data = [
            'project_id' => $project->id,
            'client_id' => $project->client_id,
            'freelancer_id' => $proposal->freelancer_id,
            'reason' => $validated['reason'],
            'status' => 'aberta',
        ];

        if (Auth::id() === $project->client_id) {
            $data['client_argument'] = $validated['argument'] ?? null;
        } else {
            $data['freelancer_argument'] = $validated['argument'] ?? null;
        }

        Dispute::create($data);

        $project->update(['status' => 'disputa']);

        $other = Auth::id() === $project->client_id ? $proposal->freelancer : $project->client;
        $this->notifications->notify(
            $other,
            'disputa_aberta',
            'Disputa aberta',
            "Uma disputa foi aberta no projeto \"{$project->title}\". Valores em custódia bloqueados.",
            ['project_id' => $project->id]
        );

        return redirect()->route('disputes.index')
            ->with('success', 'Disputa aberta. Nossa equipe vai analisar.');
    }

    /**
     * Detalhes da disputa.
     */
    public function show(Dispute $dispute)
    {
        $user = Auth::user();
        if (! $user->isAdmin() && ! in_array($user->id, [$dispute->client_id, $dispute->freelancer_id], true)) {
            abort(403);
        }

        $dispute->load(['project', 'client', 'freelancer', 'resolver']);

        return view('disputes.show', compact('dispute'));
    }

    /**
     * Admin resolve disputa (vencedor ou parcial).
     */
    public function resolve(Request $request, Dispute $dispute)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'winner' => ['required', 'in:cliente,freelancer,parcial'],
            'partial_percentage' => ['required_if:winner,parcial', 'nullable', 'numeric', 'min:0', 'max:100'],
            'resolution_notes' => ['required', 'string', 'max:2000'],
        ]);

        $dispute->update([
            'status' => 'resolvida',
            'winner' => $validated['winner'],
            'partial_percentage' => $validated['winner'] === 'parcial' ? $validated['partial_percentage'] : null,
            'resolution_notes' => $validated['resolution_notes'],
            'resolved_by' => Auth::id(),
            'resolved_at' => now(),
        ]);

        // Efetivar no escrow
        $project = $dispute->project;
        $payment = $project->payments()->where('status', 'custodia')->first();

        if ($payment) {
            if ($validated['winner'] === 'freelancer') {
                $payment->update(['status' => 'liberado', 'released_at' => now()]);
                $project->update(['status' => 'concluido', 'completed_at' => now()]);
            } elseif ($validated['winner'] === 'cliente') {
                $payment->update(['status' => 'estornado']);
                $project->update(['status' => 'cancelado']);
            } else {
                // Parcial: repassa % ao freelancer, estorna restante ao cliente
                $pct = (float) $validated['partial_percentage'];
                $payment->update([
                    'status' => 'liberado',
                    'released_at' => now(),
                    'net_amount' => $payment->net_amount * ($pct / 100),
                ]);
                $project->update(['status' => 'concluido', 'completed_at' => now()]);
            }
        } else {
            $project->update(['status' => 'concluido']);
        }

        return redirect()->route('admin.disputes')
            ->with('success', 'Disputa resolvida.');
    }

    private function authorizeParticipant(Project $project): void
    {
        if (! $project->isParticipant(Auth::id()) && ! Auth::user()->isAdmin()) {
            abort(403, 'Você não participa deste projeto.');
        }
    }
}
