<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Lista projetos para a API (paridade RF10 web).
     */
    public function index(Request $request)
    {
        $query = Project::where('status', 'aguardando_propostas')
            ->with(['client', 'category', 'skills'])
            ->withCount('proposals');

        // Visibilidade privado (dono/admin/convidado)
        $query->where(function ($q) use ($request) {
            $q->where('visibility', 'publico');
            $user = $request->user();
            if ($user) {
                $q->orWhere(function ($inner) use ($user) {
                    $inner->where('visibility', 'privado')
                        ->where(function ($w) use ($user) {
                            $w->where('client_id', $user->id);
                            if ($user->isAdmin()) {
                                $w->orWhereNotNull('id');
                            }
                            if ($user->isFreelancer()) {
                                $w->orWhereHas('invites', fn ($i) => $i->where('freelancer_id', $user->id));
                            }
                        });
                });
            }
        });

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }

        if ($request->filled('skill')) {
            $query->whereHas('skills', fn ($q) => $q->where('skills.id', $request->skill));
        }

        if ($request->filled('budget_min')) {
            $query->where('budget_max', '>=', (float) $request->budget_min);
        }
        if ($request->filled('budget_max')) {
            $query->where('budget_min', '<=', (float) $request->budget_max);
        }

        match ($request->order) {
            'recentes' => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
            'menor_orcamento' => $query->orderBy('budget_min')->orderByDesc('created_at'),
            'maior_orcamento' => $query->orderByDesc('budget_max')->orderByDesc('created_at'),
            'mais_propostas' => $query->orderByDesc('proposals_count')->orderByDesc('created_at'),
            default => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
        };

        $projects = $query->paginate(20)->withQueryString();

        return response()->json([
            'success' => true,
            'data' => ProjectResource::collection($projects),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ],
        ]);
    }

    /**
     * Retorna um projeto específico.
     */
    public function show(Project $project)
    {
        $project->load(['client', 'category', 'skills', 'proposals.freelancer.profile']);

        return response()->json([
            'success' => true,
            'data' => new ProjectResource($project),
        ]);
    }

    /**
     * Lista categorias para a API.
     */
    public function categories()
    {
        $categories = Category::active()->orderBy('sort_order')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }

    /**
     * Lista habilidades para a API.
     */
    public function skills()
    {
        $skills = Skill::with('category')->get();

        return response()->json([
            'success' => true,
            'data' => $skills,
        ]);
    }
}
