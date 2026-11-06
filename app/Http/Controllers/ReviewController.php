<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Review;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    /**
     * Formulário de avaliação.
     */
    public function create(Project $project)
    {
        $this->authorizeReviewer($project);

        $reviewed = Review::where('project_id', $project->id)
            ->where('from_user_id', Auth::id())
            ->exists();

        if ($reviewed) {
            return redirect()->route('projects.show', $project)
                ->with('info', 'Você já avaliou este projeto.');
        }

        return view('reviews.create', compact('project'));
    }

    /**
     * Salva avaliação (1-5 estrelas, em até 14 dias após conclusão).
     */
    public function store(Request $request, Project $project)
    {
        $this->authorizeReviewer($project);

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ], [
            'rating.required' => 'A avaliação é obrigatória.',
            'rating.min' => 'A nota mínima é 1.',
            'rating.max' => 'A nota máxima é 5.',
        ]);

        if ($project->completed_at && $project->completed_at->lt(now()->subDays(14))) {
            return back()->with('error', 'O prazo de 14 dias para avaliação expirou.');
        }

        if (Review::where('project_id', $project->id)->where('from_user_id', Auth::id())->exists()) {
            return back()->with('error', 'Você já avaliou este projeto.');
        }

        $toUserId = $this->otherParticipantId($project);

        $review = Review::create([
            'project_id' => $project->id,
            'from_user_id' => Auth::id(),
            'to_user_id' => $toUserId,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'is_public' => true,
        ]);

        $toUser = User::find($toUserId);
        $this->notifications->notify(
            $toUser,
            'avaliacao_recebida',
            'Nova avaliação recebida',
            "Você recebeu {$validated['rating']} estrela(s) no projeto \"{$project->title}\".",
            ['project_id' => $project->id, 'review_id' => $review->id]
        );

        return redirect()->route('projects.show', $project)
            ->with('success', 'Avaliação enviada. Obrigado!');
    }

    private function authorizeReviewer(Project $project): void
    {
        if ($project->status !== 'concluido') {
            abort(403, 'Apenas projetos concluídos podem ser avaliados.');
        }

        if (! $project->isParticipant(Auth::id())) {
            abort(403, 'Você não participou deste projeto.');
        }
    }

    private function otherParticipantId(Project $project): int
    {
        if (Auth::id() === $project->client_id) {
            return $project->acceptedProposal->freelancer_id;
        }

        return $project->client_id;
    }
}
