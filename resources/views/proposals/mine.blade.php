<x-layout title="Minhas Propostas - FreelaJob">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Minhas Propostas</h1>
            <p class="text-gray-600">Acompanhe o status das propostas que você enviou.</p>
        </div>
        <a href="{{ route('projects.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm shadow-lg">
            <i data-lucide="search" class="w-4 h-4"></i> Buscar projetos
        </a>
    </div>

    @if ($proposals->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <i data-lucide="file-text" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
            <h3 class="text-lg font-medium text-gray-900">Nenhuma proposta enviada</h3>
            <p class="text-gray-500 mb-6">Encontre um projeto e envie sua primeira proposta.</p>
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">
                Ver projetos abertos
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($proposals as $proposal)
                @php
                    $statusStyles = [
                        'pendente' => 'bg-amber-100 text-amber-700',
                        'aceita' => 'bg-green-100 text-green-700',
                        'rejeitada' => 'bg-red-100 text-red-700',
                        'retirada' => 'bg-gray-100 text-gray-600',
                    ];
                    $statusLabels = [
                        'pendente' => 'Pendente',
                        'aceita' => 'Aceita',
                        'rejeitada' => 'Rejeitada',
                        'retirada' => 'Retirada',
                    ];
                @endphp
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <a href="{{ route('projects.show', $proposal->project) }}" class="text-lg font-bold text-gray-900 hover:text-green-600">
                                    {{ $proposal->project->title }}
                                </a>
                                <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusStyles[$proposal->status] ?? 'bg-gray-100 text-gray-600' }}">
                                    {{ $statusLabels[$proposal->status] ?? ucfirst($proposal->status) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $proposal->project->category?->name ?? 'Categoria' }} • enviada em {{ $proposal->created_at->format('d/m/Y') }}
                            </p>
                            <p class="text-sm text-gray-700 mt-3 line-clamp-2">{{ $proposal->details }}</p>
                        </div>

                        <div class="shrink-0 text-left md:text-right">
                            <p class="text-xl font-bold text-green-600">R$ {{ number_format($proposal->offer, 2, ',', '.') }}</p>
                            <p class="text-xs text-gray-500">{{ $proposal->estimated_days }} dias estimados</p>

                            @if ($proposal->status === 'pendente' && $proposal->project->status === 'aguardando_propostas')
                                <form method="POST" action="{{ route('proposals.withdraw', $proposal) }}" class="mt-3"
                                      onsubmit="return confirm('Retirar esta proposta? Você poderá enviar outra enquanto o projeto estiver aberto.')">
                                    @csrf
                                    <button class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                                        Retirar proposta
                                    </button>
                                </form>
                            @elseif ($proposal->status === 'aceita' && $proposal->project->status === 'aguardando_pagamento')
                                <a href="{{ route('projects.show', $proposal->project) }}" class="mt-3 inline-block px-4 py-2 bg-blue-100 text-blue-700 rounded-lg text-sm font-medium">
                                    Aguardando pagamento
                                </a>
                            @elseif ($proposal->status === 'aceita')
                                <a href="{{ route('projects.show', $proposal->project) }}" class="mt-3 inline-block px-4 py-2 bg-green-100 text-green-700 rounded-lg text-sm font-medium">
                                    Abrir projeto
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">{{ $proposals->links() }}</div>
    @endif
</x-layout>
