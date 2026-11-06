<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;

class HomeController extends Controller
{
    /**
     * Landing page com estatísticas reais da plataforma.
     */
    public function __invoke()
    {
        $stats = [
            'freelancers' => User::where('type', 'freelancer')->where('status', 'ativo')->count(),
            'completed_projects' => Project::where('status', 'concluido')->count(),
            'open_projects' => Project::where('status', 'aguardando_propostas')->count(),
            'paid_to_freelancers' => (float) Payment::where('status', 'liberado')->sum('net_amount'),
        ];

        $categories = Category::active()
            ->withCount(['projects as open_projects_count' => fn ($query) => $query->where('status', 'aguardando_propostas')])
            ->orderByDesc('open_projects_count')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $projects = Project::where('status', 'aguardando_propostas')
            ->where('visibility', 'publico')
            ->with(['category', 'client'])
            ->withCount('proposals')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        return view('welcome', compact('stats', 'categories', 'projects'));
    }
}
