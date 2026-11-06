<?php use Illuminate\Support\Str; ?>

<x-layout title="{{ $category->name }} - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <!-- Header da categoria -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-{{ $category->color }} to-{{ $category->color }}/80 rounded-xl flex items-center justify-center">
                        <i data-lucide="{{ $category->icon ?? 'code-2' }}" class="w-8 h-8 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $category->name }}</h1>
                        <p class="text-gray-600">{{ $category->description ?? '' }}</p>
                    </div>
                </div>
                <a href="{{ route('categories.index') }}"
                   class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Voltar às categorias
                </a>
            </div>
        </div>

        <!-- Projetos da categoria -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar de filtros -->
            <div class="lg:w-64 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-6">Filtros</h3>

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Nível de experiência</label>
                            <div class="space-y-2">
                                <a href="?experience_level=iniciante"
                                   class="block px-3 py-2 rounded-lg text-sm {{ request('experience_level') === 'iniciante' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Iniciante
                                </a>
                                <a href="?experience_level=intermediario"
                                   class="block px-3 py-2 rounded-lg text-sm {{ request('experience_level') === 'intermediario' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Intermediário
                                </a>
                                <a href="?experience_level=especialista"
                                   class="block px-3 py-2 rounded-lg text-sm {{ request('experience_level') === 'especialista' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
                                    Especialista
                                </a>
                            </div>
                        </div>

                        <a href="{{ route('categories.show', $category) }}"
                           class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 rounded-lg text-center text-sm font-medium text-gray-700 transition-colors">
                            Limpar filtros
                        </a>
                    </div>
                </div>
            </div>

            <!-- Lista de projetos -->
            <div class="flex-1">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">
                        Projetos em {{ $category->name }}
                    </h2>
                    <span class="text-sm text-gray-500">
                        {{ $projects->total() }} projeto{{ $projects->total() > 1 ? 's' : '' }}
                    </span>
                </div>

                @if ($projects->isEmpty())
                    <div class="text-center py-20">
                        <i data-lucide="briefcase" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900">Nenhum projeto disponível</h3>
                        <p class="text-gray-500">Não há projetos nesta categoria aguardando propostas no momento.</p>
                        @auth
                            @if (auth()->user()->isCliente())
                                <a href="{{ route('projects.create') }}"
                                   class="mt-4 inline-block bg-green-600 hover:bg-green-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors">
                                    Criar um projeto
                                </a>
                            @endif
                        @endauth
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($projects as $project)
                            <article class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                                <div class="p-6">
                                    <div class="flex justify-between items-start mb-3">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            {{ $project->proposal_deadline->format('d/m') }}
                                        </span>
                                        <a href="{{ route('projects.show', $project) }}"
                                           class="text-right text-green-600 hover:text-green-700 text-sm font-medium">
                                            Ver detalhes →
                                        </a>
                                    </div>

                                    <h3 class="text-lg font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors">
                                        {{ $project->title }}
                                    </h3>

                                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                        {{ Str::limit($project->description, 120) }}
                                    </p>

                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-sm text-gray-500">
                                            {{ $project->client->name }}
                                        </span>
                                        @if ($project->budget_min && $project->budget_max)
                                            <span class="text-sm font-semibold text-gray-900">
                                                R$ {{ number_format($project->budget_min, 2, ',', '.') }} - R$ {{ number_format($project->budget_max, 2, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex gap-2">
                                        @foreach ($project->skills as $skill)
                                            <span class="px-2 py-1 rounded bg-gray-50 text-xs text-gray-600">
                                                {{ $skill->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    <!-- Paginação -->
                    <div class="mt-8">
                        {{ $projects->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-layout>
