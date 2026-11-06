<x-layout title="Portfólio - FreelaJob">
<div class="max-w-4xl mx-auto py-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Meu portfólio</h1>
            <p class="text-gray-600 text-sm">Itens públicos aparecem no seu perfil público.</p>
        </div>
        <a href="{{ route('freelancers.show', auth()->user()) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-medium">
            <i data-lucide="eye" class="w-4 h-4"></i> Ver perfil público
        </a>
    </div>

    <!-- Uso do plano -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6">
        <div class="flex flex-wrap items-center justify-between gap-2">
            <p class="text-sm font-medium text-gray-700">
                Plano <strong class="text-gray-900">{{ $usage['plan'] }}</strong>:
                <strong class="text-gray-900">{{ $usage['used'] }}/{{ $usage['limit'] }}</strong> itens
                <span class="text-gray-400">({{ $usage['public'] }} públicos · {{ $usage['private'] }} privados)</span>
            </p>
            @if ($usage['used'] >= $usage['limit'])
                <span class="text-xs font-medium bg-amber-100 text-amber-700 px-2.5 py-1 rounded-full">Limite atingido</span>
            @endif
        </div>
        <div class="h-2 rounded-full bg-gray-100 mt-3 overflow-hidden">
            <div class="h-full bg-green-600 rounded-full" style="width: {{ $usage['limit'] > 0 ? min(100, round(($usage['used'] / $usage['limit']) * 100)) : 0 }}%"></div>
        </div>
    </div>

    <!-- Novo item -->
    <form method="POST" action="{{ route('portfolio.store') }}" enctype="multipart/form-data"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-4">
        @csrf
        <h2 class="font-bold text-gray-900">Adicionar item</h2>
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1.5">Título *</label>
            <input id="title" type="text" name="title" required maxlength="150" value="{{ old('title') }}"
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none"
                   placeholder="Ex.: E-commerce Laravel completo">
            @error('title')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">Descrição</label>
            <textarea id="description" name="description" maxlength="2000" rows="3"
                      class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none"
                      placeholder="O que foi feito, tecnologias e resultado...">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label for="external_url" class="block text-sm font-medium text-gray-700 mb-1.5">URL externa</label>
                <input id="external_url" type="url" name="external_url" value="{{ old('external_url') }}" placeholder="https://..."
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none">
                @error('external_url')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1.5">Imagem</label>
                <input id="image" type="file" name="image" accept="image/*"
                       class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
                @error('image')
                    <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                @enderror
            </div>
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" name="is_public" value="1" {{ old('is_public', '1') ? 'checked' : '' }}
                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
            Publicar imediatamente no meu perfil público
        </label>
        <button class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold">Adicionar item</button>
    </form>

    <!-- Lista -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
        @forelse ($items as $item)
            <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @if ($item->file_path)
                    <img src="{{ asset('storage/'.$item->file_path) }}" class="w-full h-44 object-cover" alt="{{ $item->title }}">
                @else
                    <div class="w-full h-44 bg-gray-100 flex items-center justify-center text-gray-300">
                        <i data-lucide="image" class="w-10 h-10"></i>
                    </div>
                @endif
                <div class="p-5">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-bold text-gray-900 text-sm">{{ $item->title }}</p>
                        @if ($item->is_public)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 shrink-0">
                                <i data-lucide="eye" class="w-3 h-3"></i> Público
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 shrink-0">
                                <i data-lucide="eye-off" class="w-3 h-3"></i> Privado
                            </span>
                        @endif
                    </div>
                    @if ($item->description)
                        <p class="text-xs text-gray-600 mt-1.5 leading-relaxed">{{ $item->description }}</p>
                    @endif
                    @if ($item->external_url)
                        <a href="{{ $item->external_url }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 hover:text-green-700 mt-2">
                            Ver projeto <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif

                    <div class="flex flex-wrap gap-2 mt-4">
                        <form method="POST" action="{{ route('portfolio.toggle', $item) }}">
                            @csrf
                            <button class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium">
                                {{ $item->is_public ? 'Tornar privado' : 'Publicar' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('portfolio.destroy', $item) }}"
                              onsubmit="return confirm('Remover este item do portfólio?')">
                            @csrf
                            @method('DELETE')
                            <button class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium">Remover</button>
                        </form>
                    </div>

                    <details class="mt-4 border-t border-gray-100 pt-3">
                        <summary class="text-xs font-semibold text-gray-600 cursor-pointer hover:text-green-600">Editar item</summary>
                        <form method="POST" action="{{ route('portfolio.update', $item) }}" enctype="multipart/form-data" class="space-y-3 mt-3">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Título</label>
                                <input type="text" name="title" required maxlength="150" value="{{ old('title', $item->title) }}"
                                       class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Descrição</label>
                                <textarea name="description" maxlength="2000" rows="2"
                                          class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-500">{{ old('description', $item->description) }}</textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">URL externa</label>
                                <input type="url" name="external_url" value="{{ old('external_url', $item->external_url) }}"
                                       class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-500">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-1">Nova imagem (opcional)</label>
                                <input type="file" name="image" accept="image/*"
                                       class="w-full px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm">
                            </div>
                            <label class="flex items-center gap-2 text-xs text-gray-700">
                                <input type="checkbox" name="is_public" value="1" {{ old('is_public', $item->is_public ? '1' : '') ? 'checked' : '' }}
                                       class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                                Item público
                            </label>
                            <button class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold">Salvar alterações</button>
                        </form>
                    </details>
                </div>
            </article>
        @empty
            <div class="sm:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <i data-lucide="image-plus" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                <p class="font-medium text-gray-900">Nenhum item no portfólio ainda</p>
                <p class="text-sm text-gray-500 mt-1">Adicione seu primeiro projeto acima para atrair clientes.</p>
            </div>
        @endforelse
    </div>
</div>
</x-layout>
