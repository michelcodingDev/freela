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
     * Lista projetos para a API.
     */
    public function index(Request $request)
    {
        $query = Project::where('status', 'aguardando_propostas')
            ->with(['client', 'category', 'skills'])
            ->orderBy('created_at', 'desc');

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
