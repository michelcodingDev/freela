<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectRequest;
use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Lista os projetos do usuário (cliente: publicados; freelancer: contratados).
     */
    public function mine(Request $request)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.projects');
        }

        if (! $user->isCliente() && ! $user->isFreelancer()) {
            abort(403, 'Projetos indisponíveis para este tipo de conta.');
        }

        $query = $user->isCliente()
            ? Project::where('client_id', $user->id)
            : Project::whereHas('acceptedProposal', fn ($q) => $q->where('freelancer_id', $user->id));

        $query->with(['category', 'client', 'acceptedProposal.freelancer'])
            ->withCount('proposals')
            ->orderByDesc('created_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $projects = $query->paginate(15)->withQueryString();

        return view('projects.mine', compact('projects'));
    }

    /**
     * Lista projetos disponíveis para propostas (RF10: palavra, categoria, habilidade, preço, nível).
     */
    public function index(Request $request)
    {
        $query = Project::where('status', 'aguardando_propostas')
            ->with(['client', 'category', 'skills'])
            ->withCount('proposals');

        // Visibilidade: público + privados do cliente logado ou convites
        $query->where(function ($q) {
            $q->where('visibility', 'publico');
            if (Auth::check()) {
                $user = Auth::user();
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

        // Filtro por habilidade
        if ($request->filled('skill')) {
            $query->whereHas('skills', fn ($q) => $q->where('skills.id', $request->skill));
        }

        // Filtro por preço (budget_min / budget_max sobrepostos)
        if ($request->filled('budget_min')) {
            $query->where('budget_max', '>=', (float) $request->budget_min);
        }
        if ($request->filled('budget_max')) {
            $query->where('budget_min', '<=', (float) $request->budget_max);
        }

        // Ordenação: destacados primeiro, depois recentes
        match ($request->order) {
            'recentes' => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
            'menor_orcamento' => $query->orderBy('budget_min')->orderByDesc('created_at'),
            'maior_orcamento' => $query->orderByDesc('budget_max')->orderByDesc('created_at'),
            'mais_propostas' => $query->orderByDesc('proposals_count')->orderByDesc('created_at'),
            default => $query->orderByDesc('is_featured')->orderByDesc('created_at'),
        };

        $projects = $query->paginate(12)->withQueryString();
        $categories = Cache::remember('categories_active', 3600, fn () => Category::active()->orderBy('sort_order')->get());
        $skills = Cache::remember('skills_all', 3600, fn () => Skill::with('category')->orderBy('name')->get());

        return view('projects.index', compact('projects', 'categories', 'skills'));
    }

    /**
     * Exibe detalhes de um projeto.
     */
    public function show(Project $project)
    {
        if (! $project->isVisibleTo(Auth::user()) && ! Auth::user()?->isAdmin()) {
            abort(403, 'Este projeto é privado.');
        }

        $project->load(['client', 'category', 'proposals.freelancer.profile', 'acceptedProposal.freelancer', 'payments', 'attachments', 'reviews.fromUser.profile', 'reviews.toUser', 'invites']);

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

        if (! in_array($project->status, ['aguardando_aprovacao', 'aguardando_propostas', 'aguardando_pagamento'], true)) {
            return back()->with('error', 'Este projeto não pode ser fechado neste estado.');
        }

        $project->update(['status' => 'fechado']);

        return back()->with('success', 'Projeto fechado com sucesso.');
    }

    /**
     * Reabre projeto fechado (volta para aguardando propostas).
     */
    public function reopen(Project $project)
    {
        $this->authorizeProjectOwner($project);

        if ($project->status !== 'fechado') {
            return back()->with('error', 'Apenas projetos fechados podem ser reabertos.');
        }

        if ($project->proposal_deadline->isPast()) {
            return back()->with('error', 'O prazo de propostas já expirou. Edite o projeto para definir nova data.');
        }

        $project->update(['status' => 'aguardando_propostas']);

        return back()->with('success', 'Projeto reaberto para novas propostas.');
    }

    /**
     * Turbina projeto (destaque R$ 19,90, simulado).
     */
    public function boost(Project $project)
    {
        $this->authorizeProjectOwner($project);

        if (! in_array($project->status, ['aguardando_aprovacao', 'aguardando_propostas'], true)) {
            return back()->with('error', 'Este projeto não pode ser turbinado neste estado.');
        }

        if ($project->is_featured) {
            return back()->with('info', 'Este projeto já está em destaque.');
        }

        $project->update(['is_featured' => true, 'featured_fee' => 19.90]);

        return back()->with('success', 'Projeto turbinado! Ele aparecerá em destaque.');
    }

    /**
     * Aprova projeto (admin) → aguardando propostas.
     */
    public function approve(Project $project)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($project->status !== 'aguardando_aprovacao') {
            return back()->with('error', 'Este projeto não está aguardando aprovação.');
        }

        $project->update(['status' => 'aguardando_propostas', 'approved_at' => now()]);

        return back()->with('success', 'Projeto aprovado.');
    }

    /**
     * Reprova projeto (admin).
     */
    public function reject(Project $project)
    {
        if (! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($project->status !== 'aguardando_aprovacao') {
            return back()->with('error', 'Este projeto não está aguardando aprovação.');
        }

        $project->update(['status' => 'reprovado']);

        return back()->with('success', 'Projeto reprovado.');
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
