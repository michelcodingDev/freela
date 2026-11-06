<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Chat de um projeto (apenas cliente dono ou freelancer contratado).
     */
    public function index(Project $project)
    {
        $this->authorizeParticipant($project);

        $project->load(['client', 'category', 'acceptedProposal.freelancer']);
        $messages = $project->messages()->with('user')->orderBy('created_at')->paginate(50);

        // Marcar como lidas as mensagens dos outros
        $project->messages()
            ->where('is_read', false)
            ->where('user_id', '!=', Auth::id())
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('messages.index', compact('project', 'messages'));
    }

    /**
     * Enviar mensagem no chat do projeto.
     */
    public function store(Request $request, Project $project)
    {
        $this->authorizeParticipant($project);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:5000'],
        ], [
            'message.required' => 'A mensagem é obrigatória.',
        ]);

        // Bloqueio de contato externo (RF07)
        if ($this->containsExternalContact($validated['message'])) {
            return back()->with('error', 'Por segurança, contato externo (e-mail, telefone ou links) é bloqueado neste chat. Use as ferramentas da plataforma.');
        }

        $message = $project->messages()->create([
            'user_id' => Auth::id(),
            'message' => $validated['message'],
            'is_read' => false,
        ]);

        $other = $this->otherParticipant($project);
        $this->notifications->notify(
            $other,
            'mensagem_recebida',
            'Nova mensagem no projeto',
            "Você recebeu uma mensagem no projeto \"{$project->title}\".",
            ['project_id' => $project->id]
        );

        return redirect()->route('projects.messages.index', $project)
            ->with('success', 'Mensagem enviada.');
    }

    /**
     * Verifica se o usuário participa do projeto.
     */
    private function authorizeParticipant(Project $project): void
    {
        if (! $project->isParticipant(Auth::id()) && ! Auth::user()->isAdmin()) {
            abort(403, 'Você não tem permissão para acessar este chat.');
        }
    }

    private function otherParticipant(Project $project): ?User
    {
        if (Auth::id() === $project->client_id) {
            return $project->acceptedProposal?->freelancer;
        }

        return $project->client;
    }

    /**
     * Detecta e-mail, telefone ou URL (bloqueio RF07).
     */
    private function containsExternalContact(string $text): bool
    {
        $patterns = [
            '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}\b/', // e-mail
            '/https?:\/\/|www\./i', // URL
            '/\b\d{2}\s?\d?\.?\d{4,5}-?\d{4}\b/', // telefone BR
            '/\b\d{3}\.\d{3}\.\d{3}-\d{2}\b/', // CPF
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }
}
