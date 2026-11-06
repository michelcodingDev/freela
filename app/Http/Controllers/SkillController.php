<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    public function adminIndex()
    {
        $skills = Skill::with('category')->orderBy('name')->paginate(30);

        return view('admin.skills.index', compact('skills'));
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
        $skill->delete();

        return back()->with('success', 'Habilidade excluída.');
    }
}
