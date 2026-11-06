<x-layout title="Propostas - {{ $project->title }} - FreelaJob">

    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Propostas Recebidas</h1>
                <p class="text-gray-600 text-sm">{{ $project->title }}</p>
            </div>
            <a href="{{ route('projects.show', $project) }}"
               class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Voltar ao projeto
            </a>
        </div>

        @if ($proposals->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <i data-lucide="file-text" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Nenhuma proposta ainda</h3>
                <p class="text-gray-500">Este projeto ainda não recebeu propostas.</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($proposals as $proposal)
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $proposal->freelancer->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($proposal->freelancer->name) . '&background=random' }}"
                                         class="w-12 h-12 rounded-full object-cover border-2 border-gray-100" alt="Freelancer">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900">{{ $proposal->freelancer->name }}</h3>
                                        <p class="text-sm text-gray-600">{{ $proposal->freelancer->profile?->professional_title ?? 'Freelancer' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-medium text-green-600">
                                        R$ {{ number_format($proposal->offer, 2, ',', '.') }}
                                    </span>
                                    <span class="text-xs text-gray-400">+ taxa plataforma</span>
                                </div>
                            </div>

                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-gray-500">Prazo:</span>
                                    <span class="text-sm font-medium text-gray-900">{{ $proposal->estimated_days }} dias</span>
                                </div>

                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if ($proposal->status === 'pendente') bg-amber-100 text-amber-700
                                    @elseif ($proposal->status === 'aceita') bg-green-100 text-green-700
                                    @elseif ($proposal->status === 'rejeitada') bg-red-100 text-red-700
                                    @elseif ($proposal->status === 'retirada') bg-gray-100 text-gray-600
                                    @endif">
                                    @if ($proposal->status === 'pendente')
                                        <i data-lucide="clock" class="w-3 h-3"></i>
                                        Pendente
                                    @elseif ($proposal->status === 'aceita')
                                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                                        Aceita
                                    @elseif ($proposal->status === 'retirada')
                                        <i data-lucide="undo-2" class="w-3 h-3"></i>
                                        Retirada
                                    @else
                                        <i data-lucide="x-circle" class="w-3 h-3"></i>
                                        Rejeitada
                                    @endif
                                </span>
                            </div>

                            <div class="prose prose-sm max-w-none text-gray-700 leading-relaxed mb-6">
                                {{ $proposal->details }}
                            </div>

                            @if ($proposal->status === 'pendente')
                                <div class="flex gap-3">
                                    <form method="POST" action="{{ route('proposals.accept', [$project, $proposal]) }}"
                                          onsubmit="return confirm('Tem certeza que deseja aceitar esta proposta? Isso encerrará o projeto para outros freelancers.')">
                                        @csrf
                                        <button type="submit"
                                                class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-all shadow-sm">
                                            Aceitar Proposta
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('proposals.reject', [$project, $proposal]) }}">
                                        @csrf
                                        <button type="submit"
                                                class="flex-1 py-3 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl font-medium transition-all shadow-sm">
                                            Rejeitar
                                        </button>
                                    </form>
                                </div>
                            @elseif ($proposal->status === 'aceita')
                                <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                                    <p class="text-sm text-green-700 flex items-center gap-2">
                                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                                        Proposta aceita - aguardando pagamento
                                    </p>
                                </div>
                            @else
                                <div class="bg-red-50 p-4 rounded-xl border border-red-100">
                                    <p class="text-sm text-red-700 flex items-center gap-2">
                                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                                        Proposta rejeitada
                                    </p>
                                </div>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>

</x-layout>
