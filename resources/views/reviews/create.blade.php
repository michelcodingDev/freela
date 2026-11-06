<x-layout title="Avaliar Projeto - FreelaJob">
<div class="max-w-2xl mx-auto py-8">
    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar ao projeto
    </a>

    <h1 class="text-2xl font-bold text-gray-900 mt-3">Avaliar projeto</h1>
    <p class="text-gray-600 text-sm">{{ $project->title }}</p>

    @if ($toUser)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6 flex items-center gap-4">
            <img src="{{ $toUser->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($toUser->name).'&background=random&size=112' }}"
                 class="w-14 h-14 rounded-full object-cover" alt="Foto de {{ $toUser->name }}">
            <div class="min-w-0">
                <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Você está avaliando</p>
                <p class="font-bold text-gray-900">{{ $toUser->name }}</p>
                <p class="text-sm text-green-700">
                    {{ $toUser->type === 'freelancer' ? 'Freelancer' : 'Cliente' }}
                    @if ($toUser->type === 'freelancer' && $toUser->profile?->professional_title)
                        · {{ $toUser->profile->professional_title }}
                    @endif
                </p>
            </div>
        </div>
    @endif

    @if ($deadline)
        <div class="mt-4 rounded-xl border px-4 py-3 text-sm flex items-center gap-2 {{ $daysLeft <= 3 ? 'bg-red-50 border-red-200 text-red-700' : 'bg-blue-50 border-blue-200 text-blue-700' }}">
            <i data-lucide="timer" class="w-4 h-4 shrink-0"></i>
            <span>
                Prazo de avaliação: até <strong>{{ $deadline->format('d/m/Y') }}</strong>
                @if ($daysLeft > 1)
                    (faltam {{ $daysLeft }} dias)
                @elseif ($daysLeft === 1)
                    (falta 1 dia)
                @else
                    (último dia)
                @endif
            </span>
        </div>
    @endif

    <form method="POST" action="{{ route('projects.reviews.store', $project) }}"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-6"
          x-data="{ rating: {{ (int) old('rating', 0) }}, comment: @js(old('comment', '')) }">
        @csrf

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nota (1 a 5 estrelas) *</label>
            <div class="flex items-center gap-1.5" role="radiogroup" aria-label="Nota de 1 a 5 estrelas">
                @for ($i = 1; $i <= 5; $i++)
                    <label class="cursor-pointer" title="{{ $i }} estrela(s)">
                        <input type="radio" name="rating" value="{{ $i }}" class="sr-only" required
                               {{ (int) old('rating', 0) === $i ? 'checked' : '' }}
                               @change="rating = {{ $i }}">
                        <span class="block text-3xl leading-none transition {{ (int) old('rating', 0) >= $i ? 'text-amber-400' : 'text-gray-200 hover:text-amber-300' }}"
                              x-bind:class="rating >= {{ $i }} ? 'text-amber-400' : 'text-gray-200 hover:text-amber-300'">★</span>
                    </label>
                @endfor
                <span class="text-sm text-gray-500 ml-2" x-text="rating > 0 ? rating + ' estrela(s)' : 'Selecione uma nota'"></span>
            </div>
            @error('rating')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="comment" class="block text-sm font-medium text-gray-700 mb-1">Comentário</label>
            <textarea id="comment" name="comment" rows="4" maxlength="2000"
                      class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm"
                      placeholder="Como foi a experiência neste projeto?"
                      x-model="comment">{{ old('comment') }}</textarea>
            <div class="flex items-center justify-between mt-1">
                <p class="text-xs text-gray-400">Seja específico: prazo, comunicação e qualidade da entrega.</p>
                <p class="text-xs text-gray-400 shrink-0 ml-3"><span x-text="comment.length"></span>/2000</p>
            </div>
            @error('comment')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>

        <div class="rounded-xl bg-gray-50 border border-gray-100 p-4">
            <label class="flex items-start gap-3 cursor-pointer">
                <input type="checkbox" name="is_public" value="1" {{ old('is_public', '1') ? 'checked' : '' }}
                       class="w-4 h-4 mt-0.5 text-green-600 border-gray-300 rounded focus:ring-green-500">
                <span>
                    <span class="block text-sm font-medium text-gray-800">Tornar esta avaliação pública</span>
                    <span class="block text-xs text-gray-500 mt-0.5">
                        Avaliações públicas aparecem no perfil do avaliado e na página do projeto. Desmarque para enviar uma avaliação privada.
                    </span>
                </span>
            </label>
        </div>

        <button class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2">
            <i data-lucide="star" class="w-5 h-5"></i> Enviar avaliação
        </button>
    </form>
</div>
</x-layout>
