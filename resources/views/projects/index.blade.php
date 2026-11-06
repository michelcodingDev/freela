<?php use Illuminate\Support\Str; ?>

<x-layout title="Projetos - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Projetos Disponíveis</h1>
                <p class="text-gray-600">Encontre o projeto perfeito para suas habilidades.</p>
            </div>

            @auth
                @if (auth()->user()->isCliente())
                    <a href="{{ route('projects.create') }}"
                       class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2 transition-all shadow-lg">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Novo Projeto
                    </a>
                @endif
            @endauth
        </div>

        <!-- Filtros rápidos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <form method="GET" action="{{ route('projects.index') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar projetos</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Digite palavras-chave..."
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                </div>

                <div class="min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                    <select name="category" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        <option value="">Todas as categorias</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nível de experiência</label>
                    <select name="experience_level" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        <option value="">Todos os níveis</option>
                        <option value="iniciante" {{ request('experience_level') === 'iniciante' ? 'selected' : '' }}>Iniciante</option>
                        <option value="intermediario" {{ request('experience_level') === 'intermediario' ? 'selected' : '' }}>Intermediário</option>
                        <option value="especialista" {{ request('experience_level') === 'especialista' ? 'selected' : '' }}>Especialista</option>
                    </select>
                </div>

                <button type="submit"
                        class="w-full md:w-auto px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all shadow-lg">
                    Filtrar
                </button>

                <a href="{{ route('projects.index') }}"
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                    Limpar
                </a>
            </form>
        </div>

        <!-- Lista de projetos -->
        @if ($projects->isEmpty())
            <div class="text-center py-20">
                <i data-lucide="briefcase" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Nenhum projeto encontrado</h3>
                <p class="text-gray-500">Tente ajustar os filtros de busca.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($projects as $project)
                    <article class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition-all group">
                        <div class="p-6">
                            <div class="flex justify-between items-start mb-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    {{ $project->proposal_deadline->format('d/m/Y') }}
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
                                <span class="text-sm text-gray-500 capitalize">
                                    {{ $project->experience_level }}
                                </span>
                                @if ($project->budget_min && $project->budget_max)
                                    <span class="text-sm font-semibold text-gray-900">
                                        R$ {{ number_format($project->budget_min, 2, ',', '.') }} - R$ {{ number_format($project->budget_max, 2, ',', '.') }}
                                    </span>
                                @endif
                            </div>

                            <div class="flex gap-2 mb-4">
                                @foreach ($project->skills as $skill)
                                    <span class="px-2 py-1 rounded bg-gray-50 text-xs text-gray-600">
                                        {{ $skill->name }}
                                    </span>
                                @endforeach
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $project->client->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($project->client->name) . '&background=random' }}"
                                         class="w-8 h-8 rounded-full object-cover border-2 border-gray-100" alt="Cliente">
                                    <span class="text-xs text-gray-600">{{ $project->client->name }}</span>
                                </div>

                                @auth
                                    @if (auth()->user()->isFreelancer() && !$project->proposals->where('freelancer_id', auth()->id())->isEmpty())
                                        <button disabled
                                                class="px-4 py-2 bg-gray-200 text-gray-500 rounded-lg font-medium cursor-not-allowed">
                                            Proposta Enviada
                                        </button>
                                    @elseif (auth()->user()->isFreelancer())
                                        <a href="{{ route('proposals.create', $project) }}"
                                           class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all shadow-sm">
                                            Enviar Proposta
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Paginação -->
            <div class="mt-8 flex justify-center">
                {{ $projects->links() }}
            </div>
        @endif
    </div>

</x-layout>
