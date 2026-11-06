<?php

namespace App\Http\Controllers;

use App\Models\User;

class RankingController extends Controller
{
    /**
     * Ranking público de freelancers (avaliação pública + projetos concluídos).
     *
     * Ordenação: maior média de avaliações públicas, depois mais avaliações,
     * depois mais projetos concluídos.
     */
    public function __invoke()
    {
        $freelancers = User::where('type', 'freelancer')
            ->where('status', 'ativo')
            ->with(['profile'])
            ->withAvg(['reviewsReceived as rating_avg' => fn ($q) => $q->where('is_public', true)], 'rating')
            ->withCount(['reviewsReceived' => fn ($q) => $q->where('is_public', true)])
            ->withCount(['proposalsAsFreelancer as completed_projects' => function ($q) {
                $q->whereHas('project', fn ($p) => $p->where('status', 'concluido'));
            }])
            ->orderByDesc('rating_avg')
            ->orderByDesc('reviews_received_count')
            ->orderByDesc('completed_projects')
            ->limit(50)
            ->get();

        $podium = $freelancers->take(3);
        $rest = $freelancers->skip(3);

        return view('ranking.index', compact('freelancers', 'podium', 'rest'));
    }
}
