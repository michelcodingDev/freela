<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Lista todas as categorias ativas.
     */
    public function index()
    {
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('categories.index', compact('categories'));
    }

    /**
     * Exibe uma categoria específica com seus projetos.
     *
     * Apenas projetos públicos aguardando propostas, com filtro opcional por nível.
     */
    public function show(Request $request, Category $category)
    {
        if (! $category->is_active) {
            abort(404);
        }

        $query = $category->projects()
            ->where('status', 'aguardando_propostas')
            ->where('visibility', 'publico')
            ->with(['client', 'category'])
            ->withCount('proposals')
            ->orderByDesc('is_featured')
            ->orderByDesc('created_at');

        $level = $request->get('experience_level');
        if (in_array($level, ['iniciante', 'intermediario', 'especialista'], true)) {
            $query->where('experience_level', $level);
        }

        $projects = $query->paginate(12)->withQueryString();

        return view('categories.show', compact('category', 'projects'));
    }

    /**
     * Admin: lista todas as categorias (incluindo inativas).
     */
    public function adminIndex()
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Admin: exibe formulário para criar categoria.
     */
    public function adminCreate()
    {
        return view('admin.categories.create');
    }

    /**
     * Admin: armazena nova categoria.
     */
    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug'],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Admin: exibe formulário para editar categoria.
     */
    public function adminEdit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Admin: atualiza categoria.
     */
    public function adminUpdate(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:categories,slug,'.$category->id],
            'icon' => ['nullable', 'string', 'max:100'],
            'color' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ]);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Admin: exclui categoria (bloqueia se houver projetos vinculados).
     */
    public function adminDestroy(Category $category)
    {
        if ($category->projects()->exists()) {
            return back()->with('error', 'Não é possível excluir uma categoria com projetos vinculados.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
