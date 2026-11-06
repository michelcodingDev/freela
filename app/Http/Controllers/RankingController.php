<?php

namespace App\Http\Controllers;

use App\Models\User;

class RankingController extends Controller
{
    /**
     * Ranking público de freelancers (avaliação + projetos concluídos).
     */
    public function __invoke()
    {
        $freelancers = User::where('type', 'freelancer')
            ->where('status', 'ativo')
            ->with(['profile'])
            ->withAvg('reviewsReceived as rating_avg', 'rating')
            ->withCount('reviewsReceived')
            ->withCount(['proposalsAsFreelancer as completed_projects' => function ($q) {
                $q->whereHas('project', fn ($p) => $p->where('status', 'concluido'));
            }])
            ->orderByDesc('rating_avg')
            ->orderByDesc('completed_projects')
            ->limit(50)
            ->get();

        return view('ranking.index', compact('freelancers'));
    }
}
