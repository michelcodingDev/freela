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
     */
    public function show(Category $category)
    {
        $projects = $category->projects()
            ->where('status', 'aguardando_propostas')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

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
     * Admin: exclui categoria.
     */
    public function adminDestroy(Category $category)
    {
        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Categoria excluída com sucesso!');
    }
}
