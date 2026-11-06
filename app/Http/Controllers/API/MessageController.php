<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index(Request $request, Project $project)
    {
        if (! $project->isParticipant($request->user()->id) && ! $request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Sem acesso'], 403);
        }

        $messages = $project->messages()->with('user')->orderBy('created_at')->paginate(50);

        return response()->json(['success' => true, 'data' => $messages]);
    }

    public function store(Request $request, Project $project)
    {
        if (! $project->isParticipant($request->user()->id) && ! $request->user()->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Sem acesso'], 403);
        }

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ]);

        if ($this->containsExternalContact($validated['message'])) {
            return response()->json(['success' => false, 'message' => 'Contato externo bloqueado por segurança.'], 422);
        }

        $message = $project->messages()->create([
            'user_id' => $request->user()->id,
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $other = $request->user()->id === $project->client_id
            ? $project->acceptedProposal?->freelancer
            : $project->client;

        $this->notifications->notify($other, 'mensagem_recebida', 'Nova mensagem', "Nova mensagem no projeto \"{$project->title}\".", ['project_id' => $project->id]);

        return response()->json(['success' => true, 'data' => $message], 201);
    }

    private function containsExternalContact(string $text): bool
    {
        $patterns = [
            '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/',
            '/https?:\/\/|www\./i',
            '/\b\d{2}\s?\d?\.?\d{4,5}-?\d{4}\b/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}
