<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use Illuminate\Http\Request;

class FreelancerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('type', 'freelancer')
            ->where('status', 'ativo')
            ->with(['profile'])
            ->withAvg('reviewsReceived as rating_avg', 'rating')
            ->withCount('reviewsReceived')
            ->orderByDesc('created_at');

        $freelancers = $query->paginate(20);

        return response()->json(['success' => true, 'data' => $freelancers]);
    }

    public function show(User $freelancer)
    {
        if ($freelancer->type !== 'freelancer') {
            return response()->json(['success' => false, 'message' => 'Freelancer não encontrado'], 404);
        }

        $freelancer->load(['profile', 'portfolioItems']);
        $reviews = Review::where('to_user_id', $freelancer->id)->with(['fromUser', 'project'])->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $freelancer,
            'reviews' => $reviews,
        ]);
    }
}
