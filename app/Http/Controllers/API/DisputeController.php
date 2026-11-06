<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\Project;
use Illuminate\Http\Request;

class DisputeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $query = Dispute::with(['project'])->orderByDesc('created_at');
        if (! $user->isAdmin()) {
            $query->where(fn ($q) => $q->where('client_id', $user->id)->orWhere('freelancer_id', $user->id));
        }

        return response()->json(['success' => true, 'data' => $query->paginate(20)]);
    }

    public function store(Request $request, Project $project)
    {
        if (! $project->isParticipant($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Sem acesso'], 403);
        }

        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:20', 'max:2000'],
            'argument' => ['nullable', 'string', 'max:2000'],
        ]);

        $proposal = $project->acceptedProposal;
        if (! $proposal) {
            return response()->json(['success' => false, 'message' => 'Sem freelancer contratado'], 422);
        }

        $data = [
            'project_id' => $project->id,
            'client_id' => $project->client_id,
            'freelancer_id' => $proposal->freelancer_id,
            'reason' => $validated['reason'],
            'status' => 'aberta',
        ];
        if ($request->user()->id === $project->client_id) {
            $data['client_argument'] = $validated['argument'] ?? null;
        } else {
            $data['freelancer_argument'] = $validated['argument'] ?? null;
        }

        $dispute = Dispute::create($data);
        $project->update(['status' => 'disputa']);

        return response()->json(['success' => true, 'data' => $dispute], 201);
    }
}
