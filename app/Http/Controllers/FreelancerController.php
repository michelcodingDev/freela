<?php

namespace App\Http\Controllers;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Http\Request;

class FreelancerController extends Controller
{
    /**
     * Vitrine de freelancers (público).
     */
    public function index(Request $request)
    {
        $query = User::where('type', 'freelancer')
            ->where('status', 'ativo')
            ->with(['profile', 'portfolioItems', 'skills'])
            ->withAvg(['reviewsReceived as rating_avg' => fn ($q) => $q->where('is_public', true)], 'rating')
            ->withCount(['reviewsReceived' => fn ($q) => $q->where('is_public', true)]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('profile', function ($p) use ($search) {
                        $p->where('professional_title', 'like', "%{$search}%")
                            ->orWhere('bio', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('skill')) {
            $query->whereHas('skills', fn ($q) => $q->where('skills.id', $request->skill));
        }

        match ($request->order) {
            'melhor_avaliacao' => $query->orderByDesc('rating_avg'),
            'mais_projetos' => $query->orderByDesc('created_at'),
            default => $query->orderByDesc('created_at'),
        };

        $freelancers = $query->paginate(12)->withQueryString();
        $skills = Skill::orderBy('name')->get();

        return view('freelancers.index', compact('freelancers', 'skills'));
    }

    /**
     * Perfil público do freelancer.
     */
    public function show(User $freelancer)
    {
        if ($freelancer->type !== 'freelancer' || $freelancer->status !== 'ativo') {
            abort(404);
        }

        $freelancer->load([
            'profile',
            'skills',
            'portfolioItems' => fn ($q) => $q->where('is_public', true)->orderByDesc('created_at'),
        ]);

        $publicReviews = $freelancer->reviewsReceived()->where('is_public', true);

        $ratingAvg = (float) ($publicReviews->avg('rating') ?? 0);
        $ratingCount = (clone $publicReviews)->count();

        $ratingDistribution = (clone $publicReviews)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating')
            ->toArray();

        $distribution = [];
        for ($stars = 5; $stars >= 1; $stars--) {
            $count = (int) ($ratingDistribution[$stars] ?? 0);
            $distribution[$stars] = [
                'count' => $count,
                'percent' => $ratingCount > 0 ? round(($count / $ratingCount) * 100) : 0,
            ];
        }

        $reviews = (clone $publicReviews)
            ->with(['fromUser.profile', 'project'])
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('freelancers.show', compact(
            'freelancer',
            'reviews',
            'ratingAvg',
            'ratingCount',
            'distribution',
        ));
    }
}
