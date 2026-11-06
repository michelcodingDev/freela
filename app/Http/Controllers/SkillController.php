<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function adminIndex(Request $request)
    {
        $query = Skill::with('category')->withCount(['projects', 'users']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhereHas('category', fn ($c) => $c->where('name', 'like', '%'.$search.'%'));
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $skills = $query->orderBy('name')->paginate(30)->withQueryString();
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.skills.index', compact('skills', 'categories'));
    }

    public function adminCreate()
    {
        $categories = Category::active()->orderBy('sort_order')->get();

        return view('admin.skills.create', compact('categories'));
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:skills,name'],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        Skill::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']).'-'.Str::random(6),
            'category_id' => $validated['category_id'],
        ]);

        return redirect()->route('admin.skills.index')
            ->with('success', 'Habilidade criada.');
    }

    public function adminDestroy(Skill $skill)
    {
        if ($skill->projects()->exists() || $skill->users()->exists()) {
            return back()->with('error', 'Não é possível excluir uma habilidade em uso por projetos ou freelancers.');
        }

        $skill->delete();

        return back()->with('success', 'Habilidade excluída.');
    }

    public function adminEdit(Skill $skill)
    {
        $categories = Category::orderBy('sort_order')->get();

        return view('admin.skills.edit', compact('skill', 'categories'));
    }

    public function adminUpdate(Request $request, Skill $skill)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:skills,name,'.$skill->id],
            'category_id' => ['required', 'exists:categories,id'],
        ]);

        $skill->update($validated);

        return redirect()->route('admin.skills.index')
            ->with('success', 'Habilidade atualizada.');
    }
}
