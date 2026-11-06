<?php

namespace App\Http\Controllers;

use App\Models\PortfolioItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PortfolioController extends Controller
{
    /**
     * Lista itens do portfólio do freelancer logado.
     */
    public function index()
    {
        $items = Auth::user()->portfolioItems()->orderByDesc('created_at')->get();

        return view('portfolio.index', compact('items'));
    }

    /**
     * Salva novo item de portfólio (limite do plano).
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (! $user->isFreelancer()) {
            abort(403, 'Apenas freelancers têm portfólio.');
        }

        $maxItems = $this->maxItemsFor($user);

        if ($user->portfolioItems()->count() >= $maxItems) {
            return back()->with('error', "Você atingiu o limite de {$maxItems} itens do seu plano. Faça upgrade para adicionar mais.");
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'external_url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('portfolio', 'public');
        }

        PortfolioItem::create([
            'freelancer_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'external_url' => $validated['external_url'] ?? null,
            'file_path' => $path,
            'is_public' => $request->boolean('is_public', true),
        ]);

        return redirect()->route('portfolio.index')
            ->with('success', 'Item adicionado ao portfólio.');
    }

    /**
     * Remove item do portfólio.
     */
    public function destroy(PortfolioItem $item)
    {
        if ($item->freelancer_id !== Auth::id()) {
            abort(403);
        }

        if ($item->file_path) {
            Storage::disk('public')->delete($item->file_path);
        }

        $item->delete();

        return back()->with('success', 'Item removido.');
    }

    private function maxItemsFor(User $user): int
    {
        $sub = $user->subscriptions()->where('status', 'ativo')->where('ends_at', '>', now())->first();

        return (int) ($sub?->plan->max_portfolio_items ?? 3);
    }
}
