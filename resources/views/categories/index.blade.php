<x-layout title="Categorias - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categorias</h1>
                <p class="text-gray-600">Escolha a categoria que melhor se encaixa no seu projeto.</p>
            </div>
            <a href="{{ route('home') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">
                ← Voltar ao início
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($categories as $category)
                <a href="{{ route('categories.show', $category) }}"
                   class="group block bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all hover:border-green-200">
                    <div class="w-16 h-16 bg-gradient-to-br from-{{ $category->color }} to-{{ $category->color }}/80 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="{{ $category->icon ?? 'code-2' }}" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $category->name }}</h3>
                    <p class="text-gray-600 text-sm mb-4">{{ $category->description ?? 'Sem descrição' }}</p>
                    <div class="flex items-center justify-between px-4 pb-4">
                        <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded">
                            Ativa
                        </span>
                        <i data-lucide="chevron-right" class="w-4 h-4 text-gray-400 group-hover:text-green-600 transition-colors"></i>
                    </div>
                </a>
            @endforeach
        </div>

        @if ($categories->isEmpty())
            <div class="text-center py-20">
                <i data-lucide="folder" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Nenhuma categoria encontrada</h3>
                <p class="text-gray-500">Não há categorias disponíveis no momento.</p>
            </div>
        @endif
    </div>

</x-layout>
