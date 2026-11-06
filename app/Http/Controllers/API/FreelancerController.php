<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class FreelancerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('type', 'freelancer')
            ->where('status', 'ativo')
            ->with(['profile'])
            ->withAvg(['reviewsReceived as rating_avg' => fn ($q) => $q->where('is_public', true)], 'rating')
            ->withCount(['reviewsReceived' => fn ($q) => $q->where('is_public', true)])
            ->orderByDesc('created_at');

        $freelancers = $query->paginate(20);

        return response()->json(['success' => true, 'data' => $freelancers]);
    }

    public function show(User $freelancer)
    {
        if ($freelancer->type !== 'freelancer' || $freelancer->status !== 'ativo') {
            return response()->json(['success' => false, 'message' => 'Freelancer não encontrado'], 404);
        }

        $freelancer->load([
            'profile',
            'skills',
            'portfolioItems' => fn ($q) => $q->where('is_public', true)->orderByDesc('created_at'),
        ]);

        $ratingAvg = (float) ($freelancer->reviewsReceived()->where('is_public', true)->avg('rating') ?? 0);
        $ratingCount = $freelancer->reviewsReceived()->where('is_public', true)->count();

        $reviews = $freelancer->reviewsReceived()
            ->where('is_public', true)
            ->with(['fromUser', 'project'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $freelancer,
            'rating_avg' => $ratingAvg,
            'rating_count' => $ratingCount,
            'reviews' => $reviews,
        ]);
    }
}
