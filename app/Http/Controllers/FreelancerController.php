<?php

namespace App\Http\Controllers;

use App\Models\Review;
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
            ->withAvg('reviewsReceived as rating_avg', 'rating')
            ->withCount('reviewsReceived');

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
        if ($freelancer->type !== 'freelancer') {
            abort(404);
        }

        $freelancer->load(['profile', 'portfolioItems', 'skills']);
        $reviews = Review::where('to_user_id', $freelancer->id)
            ->with(['fromUser', 'project'])
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('freelancers.show', compact('freelancer', 'reviews'));
    }
}
