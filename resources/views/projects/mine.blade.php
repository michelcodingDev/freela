@php
    $statusStyles = [
        'aguardando_aprovacao' => 'bg-amber-100 text-amber-700',
        'aguardando_propostas' => 'bg-blue-100 text-blue-700',
        'aguardando_pagamento' => 'bg-indigo-100 text-indigo-700',
        'em_andamento' => 'bg-cyan-100 text-cyan-700',
        'aguardando_liberacao' => 'bg-purple-100 text-purple-700',
        'concluido' => 'bg-green-100 text-green-700',
        'cancelado' => 'bg-gray-100 text-gray-600',
        'disputa' => 'bg-red-100 text-red-700',
        'fechado' => 'bg-gray-100 text-gray-600',
        'reprovado' => 'bg-red-100 text-red-700',
    ];

    $statusLabels = [
        'aguardando_aprovacao' => 'Aguardando aprovação',
        'aguardando_propostas' => 'Recebendo propostas',
        'aguardando_pagamento' => 'Aguardando pagamento',
        'em_andamento' => 'Em andamento',
        'aguardando_liberacao' => 'Aguardando liberação',
        'concluido' => 'Concluído',
        'cancelado' => 'Cancelado',
        'disputa' => 'Em disputa',
        'fechado' => 'Fechado',
        'reprovado' => 'Reprovado',
    ];
@endphp

<x-layout title="Meus Projetos - FreelaJob">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Meus Projetos</h1>
            <p class="text-gray-600">
                {{ auth()->user()->isCliente() ? 'Projetos que você publicou.' : 'Projetos em que você foi contratado.' }}
            </p>
        </div>
        @if (auth()->user()->isCliente())
            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm shadow-lg">
                <i data-lucide="plus" class="w-4 h-4"></i> Novo projeto
            </a>
        @else
            <a href="{{ route('projects.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm shadow-lg">
                <i data-lucide="search" class="w-4 h-4"></i> Buscar projetos
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('projects.mine') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[220px]">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por status</label>
                <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm">
                    <option value="">Todos os status</option>
                    @foreach ($statusLabels as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Filtrar</button>
            @if (request()->filled('status'))
                <a href="{{ route('projects.mine') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Limpar</a>
            @endif
        </div>
    </form>

    @if ($projects->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <i data-lucide="folder-open" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Nenhum projeto encontrado</h3>
            <p class="text-gray-500 mt-1">
                {{ auth()->user()->isCliente() ? 'Publique seu primeiro projeto para receber propostas.' : 'Envie propostas e seus projetos contratados aparecerão aqui.' }}
            </p>
            <a href="{{ auth()->user()->isCliente() ? route('projects.create') : route('projects.index') }}"
               class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">
                {{ auth()->user()->isCliente() ? 'Publicar projeto' : 'Ver projetos abertos' }}
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($projects as $project)
                @php
                    $counterpart = auth()->user()->isCliente()
                        ? $project->acceptedProposal?->freelancer
                        : $project->client;
                @endphp
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('projects.show', $project) }}" class="text-lg font-bold text-gray-900 hover:text-green-600">
                                    {{ $project->title }}
                                </a>
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusStyles[$project->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$project->status] ?? ucfirst($project->status) }}
                                </span>
                                @if ($project->is_featured)
                                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Destaque</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-500 mt-1">
                                {{ $project->category?->name ?? 'Categoria' }}
                                • publicado em {{ $project->created_at->format('d/m/Y') }}
                                • {{ $project->proposals_count }} proposta(s)
                            </p>

                            @if (auth()->user()->isCliente())
                                <p class="text-sm text-gray-600 mt-2">
                                    Freelancer:
                                    <span class="font-medium text-gray-900">{{ $counterpart?->name ?? 'ainda não contratado' }}</span>
                                </p>
                            @else
                                <p class="text-sm text-gray-600 mt-2">
                                    Cliente:
                                    <span class="font-medium text-gray-900">{{ $counterpart?->name ?? '—' }}</span>
                                </p>
                            @endif
                        </div>

                        <div class="shrink-0 text-left md:text-right">
                            <p class="text-lg font-bold text-gray-900">
                                R$ {{ number_format($project->budget_min ?? 0, 2, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500">
                                até R$ {{ number_format($project->budget_max ?? 0, 2, ',', '.') }}
                            </p>

                            <div class="flex flex-wrap md:justify-end gap-2 mt-3">
                                <a href="{{ route('projects.show', $project) }}"
                                   class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                                    Detalhes
                                </a>
                                @if (auth()->user()->isCliente() && $project->status === 'aguardando_propostas')
                                    <a href="{{ route('proposals.index', $project) }}"
                                       class="px-4 py-2 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg text-sm font-medium">
                                        Ver propostas
                                    </a>
                                @elseif (auth()->user()->isFreelancer() && $project->isParticipant(auth()->id()))
                                    <a href="{{ route('projects.messages.index', $project) }}"
                                       class="px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm font-medium">
                                        Abrir chat
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">{{ $projects->links() }}</div>
    @endif
</x-layout>
