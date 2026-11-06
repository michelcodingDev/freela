<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index(Request $request)
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
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $freelancers]);
    }
}
