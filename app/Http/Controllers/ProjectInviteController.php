<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectInvite;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectInviteController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Cliente convida freelancer para projeto privado/público.
     */
    public function store(Request $request, Project $project)
    {
        if (Auth::id() !== $project->client_id) {
            abort(403, 'Apenas o cliente pode convidar.');
        }

        $validated = $request->validate([
            'freelancer_id' => ['required', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $freelancer = User::findOrFail($validated['freelancer_id']);
        if (! $freelancer->isFreelancer() || $freelancer->status !== 'ativo') {
            return back()->with('error', 'Freelancer inválido ou inativo.');
        }

        $invite = ProjectInvite::updateOrCreate(
            ['project_id' => $project->id, 'freelancer_id' => $freelancer->id],
            [
                'client_id' => Auth::id(),
                'status' => 'convidado',
                'message' => $validated['message'] ?? null,
            ]
        );

        $this->notifications->notify(
            $freelancer,
            'convite_projeto',
            'Convite para projeto',
            "Você foi convidado para o projeto \"{$project->title}\".",
            ['project_id' => $project->id, 'invite_id' => $invite->id]
        );

        return back()->with('success', 'Convite enviado para '.$freelancer->name);
    }

    /**
     * Lista convites recebidos (freelancer).
     */
    public function index()
    {
        $invites = Auth::user()->invitesReceived()->with(['project', 'client'])->orderByDesc('created_at')->paginate(20);

        return view('invites.index', compact('invites'));
    }

    public function accept(ProjectInvite $invite)
    {
        if ($invite->freelancer_id !== Auth::id()) {
            abort(403);
        }

        $invite->update(['status' => 'aceito', 'accepted_at' => now()]);

        return redirect()->route('projects.show', $invite->project)
            ->with('success', 'Convite aceito! Você pode enviar proposta.');
    }

    public function decline(ProjectInvite $invite)
    {
        if ($invite->freelancer_id !== Auth::id()) {
            abort(403);
        }

        $invite->update(['status' => 'recusado']);

        return redirect()->route('invites.index')
            ->with('success', 'Convite recusado.');
    }
}
