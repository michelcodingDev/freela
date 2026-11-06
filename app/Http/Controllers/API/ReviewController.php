<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function store(Request $request, Project $project)
    {
        if ($project->status !== 'concluido' || ! $project->isParticipant($request->user()->id)) {
            return response()->json(['success' => false, 'message' => 'Avaliação não permitida'], 403);
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
            'is_public' => ['sometimes', 'boolean'],
        ]);

        if ($project->completed_at && $project->completed_at->lt(now()->subDays(14))) {
            return response()->json(['success' => false, 'message' => 'Prazo de 14 dias expirado'], 422);
        }

        if (Review::where('project_id', $project->id)->where('from_user_id', $request->user()->id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Já avaliado'], 422);
        }

        $toId = $request->user()->id === $project->client_id
            ? $project->acceptedProposal?->freelancer_id
            : $project->client_id;

        if (! $toId) {
            return response()->json(['success' => false, 'message' => 'Este projeto não possui freelancer contratado para ser avaliado.'], 403);
        }

        $review = Review::create([
            'project_id' => $project->id,
            'from_user_id' => $request->user()->id,
            'to_user_id' => $toId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_public' => $request->has('is_public') ? $request->boolean('is_public') : true,
        ]);

        $this->notifications->notify(
            User::find($toId),
            'avaliacao_recebida',
            'Nova avaliação',
            "Você recebeu {$validated['rating']} estrela(s) no projeto \"{$project->title}\".",
            ['project_id' => $project->id]
        );

        return response()->json(['success' => true, 'data' => $review], 201);
    }
}
