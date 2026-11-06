<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectInvite;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class InviteController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request)
    {
        $invites = $request->user()->invitesReceived()->with(['project', 'client'])->orderByDesc('created_at')->paginate(20);

        return response()->json(['success' => true, 'data' => $invites]);
    }

    public function store(Request $request, Project $project)
    {
        if ($request->user()->id !== $project->client_id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        $validated = $request->validate([
            'freelancer_id' => ['required', 'exists:users,id'],
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        $freelancer = User::findOrFail($validated['freelancer_id']);
        if (! $freelancer->isFreelancer() || $freelancer->status !== 'ativo') {
            return response()->json(['success' => false, 'message' => 'Freelancer inválido'], 422);
        }

        $invite = ProjectInvite::updateOrCreate(
            ['project_id' => $project->id, 'freelancer_id' => $freelancer->id],
            ['client_id' => $request->user()->id, 'status' => 'convidado', 'message' => $validated['message'] ?? null]
        );

        $this->notifications->notify($freelancer, 'convite_projeto', 'Convite para projeto', "Você foi convidado para \"{$project->title}\".", ['project_id' => $project->id]);

        return response()->json(['success' => true, 'data' => $invite], 201);
    }

    public function accept(Request $request, ProjectInvite $invite)
    {
        if ($invite->freelancer_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        $invite->update(['status' => 'aceito', 'accepted_at' => now()]);

        return response()->json(['success' => true, 'data' => $invite]);
    }

    public function decline(Request $request, ProjectInvite $invite)
    {
        if ($invite->freelancer_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Não autorizado'], 403);
        }

        $invite->update(['status' => 'recusado']);

        return response()->json(['success' => true, 'data' => $invite]);
    }
}
