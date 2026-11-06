<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Lista projetos disponíveis para propostas.
     */
    public function index(Request $request)
    {
        $query = Project::where('status', 'aguardando_propostas')
            ->with(['client', 'category'])
            ->orderBy('created_at', 'desc');

        // Filtro por categoria
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filtro por busca
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filtro por nível de experiência
        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->experience_level);
        }

        $projects = $query->paginate(12)->withQueryString();
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('projects.index', compact('projects', 'categories'));
    }

    /**
     * Exibe detalhes de um projeto.
     */
    public function show(Project $project)
    {
        $project->load(['client', 'category', 'proposals.freelancer.profile']);

        return view('projects.show', compact('project'));
    }

    /**
     * Exibe formulário para criar projeto (apenas clientes).
     */
    public function create()
    {
        $categories = Category::active()->orderBy('sort_order')->get();
        $skills = Skill::with('category')->get();

        return view('projects.create', compact('categories', 'skills'));
    }

    /**
     * Armazena novo projeto.
     */
    public function store(ProjectRequest $request)
    {
        $validated = $request->validated();

        $project = Project::create([
            'client_id' => Auth::id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'experience_level' => $validated['experience_level'],
            'visibility' => $validated['visibility'],
            'status' => 'aguardando_aprovacao',
            'proposal_deadline' => $validated['proposal_deadline'],
            'budget_min' => $validated['budget_min'] ?? null,
            'budget_max' => $validated['budget_max'] ?? null,
        ]);

        // Salvar skills selecionadas
        if (isset($validated['skills'])) {
            $project->skills()->attach($validated['skills']);
        }

        // Upload de anexos
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('project_attachments', 'public');
                $project->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto enviado para análise. Será aprovado em breve.');
    }

    /**
     * Exibe formulário para editar projeto.
     */
    public function edit(Project $project)
    {
        $this->authorizeProjectOwner($project);

        $categories = Category::active()->orderBy('sort_order')->get();
        $skills = Skill::with('category')->get();
        $project->load(['skills', 'attachments']);

        return view('projects.edit', compact('project', 'categories', 'skills'));
    }

    /**
     * Atualiza projeto.
     */
    public function update(ProjectRequest $request, Project $project)
    {
        $this->authorizeProjectOwner($project);

        $validated = $request->validated();

        $project->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'experience_level' => $validated['experience_level'],
            'visibility' => $validated['visibility'],
            'proposal_deadline' => $validated['proposal_deadline'],
            'budget_min' => $validated['budget_min'] ?? null,
            'budget_max' => $validated['budget_max'] ?? null,
        ]);

        // Atualizar skills
        $project->skills()->sync(isset($validated['skills']) ? $validated['skills'] : []);

        // Novos anexos
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('project_attachments', 'public');
                $project->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'mime_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        return redirect()->route('projects.show', $project)
            ->with('success', 'Projeto atualizado com sucesso!');
    }

    /**
     * Fecha o projeto (apenas o cliente).
     */
    public function close(Project $project)
    {
        $this->authorizeProjectOwner($project);

        $project->update(['status' => 'fechado']);

        return back()->with('success', 'Projeto fechado com sucesso.');
    }

    /**
     * Verifica se o usuário logado é o dono do projeto.
     */
    private function authorizeProjectOwner(Project $project): void
    {
        if (Auth::id() !== $project->client_id) {
            abort(403, 'Você não tem permissão para realizar esta ação.');
        }
    }
}
