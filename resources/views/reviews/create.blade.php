<x-layout title="Avaliar Projeto - FreelaJob">
<div class="max-w-2xl mx-auto py-8">
    <a href="{{ route('projects.show', $project) }}" class="text-sm text-green-600 hover:text-green-700 font-medium">← Voltar ao projeto</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Avaliar projeto</h1>
    <p class="text-gray-600 text-sm">{{ $project->title }}</p>

    <form method="POST" action="{{ route('projects.reviews.store', $project) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-6">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nota (1 a 5 estrelas) *</label>
            <div class="flex gap-2">
                @for ($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer">
                        <input type="radio" name="rating" value="{{ $i }}" class="peer hidden" required>
                        <span class="block px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium peer-checked:bg-amber-400 peer-checked:text-white peer-checked:border-amber-400">{{ $i }}★</span>
                    </label>
                @endfor
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Comentário</label>
            <textarea name="comment" rows="4" maxlength="2000" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm" placeholder="Como foi a experiência?"></textarea>
        </div>
        <button class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold">Enviar avaliação</button>
    </form>
</div>
</x-layout>
