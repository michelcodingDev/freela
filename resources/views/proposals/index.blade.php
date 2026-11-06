<x-layout title="Propostas - {{ $project->title }} - FreelaJob">
    <div class="max-w-4xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Propostas recebidas</h1>
                <p class="text-gray-600 text-sm">{{ $project->title }}</p>
            </div>
            <a href="{{ route('projects.show', $project) }}"
               class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar ao projeto
            </a>
        </div>

        <!-- Resumo comparativo -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 font-medium">Total de propostas</p>
                <p class="text-xl font-bold text-gray-900 mt-1">{{ $summary['total'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 font-medium">Pendentes</p>
                <p class="text-xl font-bold text-amber-600 mt-1">{{ $summary['pending'] }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 font-medium">Menor oferta pendente</p>
                <p class="text-xl font-bold text-green-600 mt-1">
                    {{ $summary['cheapest'] !== null ? 'R$ '.number_format((float) $summary['cheapest'], 2, ',', '.') : '—' }}
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
                <p class="text-xs text-gray-500 font-medium">Menor prazo pendente</p>
                <p class="text-xl font-bold text-blue-600 mt-1">
                    {{ $summary['fastest'] !== null ? $summary['fastest'].' dias' : '—' }}
                </p>
            </div>
        </div>

        <!-- Ordenação e filtro -->
        <form method="GET" action="{{ route('proposals.index', $project) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
            <div class="flex flex-wrap items-end gap-3">
                <div class="min-w-[200px]">
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-1.5">Ordenar por</label>
                    <select id="sort" name="sort" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="recent" {{ $sort === 'recent' ? 'selected' : '' }}>Mais recentes</option>
                        <option value="offer_asc" {{ $sort === 'offer_asc' ? 'selected' : '' }}>Menor oferta</option>
                        <option value="offer_desc" {{ $sort === 'offer_desc' ? 'selected' : '' }}>Maior oferta</option>
                        <option value="days_asc" {{ $sort === 'days_asc' ? 'selected' : '' }}>Menor prazo</option>
                        <option value="rating_desc" {{ $sort === 'rating_desc' ? 'selected' : '' }}>Melhor avaliação</option>
                    </select>
                </div>
                <div class="min-w-[160px]">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                    <select id="status" name="status" class="w-full px-3.5 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 outline-none">
                        <option value="all" {{ $filter === 'all' ? 'selected' : '' }}>Todos</option>
                        <option value="pendente" {{ $filter === 'pendente' ? 'selected' : '' }}>Pendentes</option>
                        <option value="aceita" {{ $filter === 'aceita' ? 'selected' : '' }}>Aceitas</option>
                        <option value="rejeitada" {{ $filter === 'rejeitada' ? 'selected' : '' }}>Rejeitadas</option>
                        <option value="retirada" {{ $filter === 'retirada' ? 'selected' : '' }}>Retiradas</option>
                    </select>
                </div>
                <button class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Aplicar</button>
                @if ($sort !== 'recent' || $filter !== 'all')
                    <a href="{{ route('proposals.index', $project) }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Limpar</a>
                @endif
            </div>
        </form>

        @if ($project->status !== 'aguardando_propostas')
            <div class="bg-blue-50 border border-blue-200 text-blue-800 px-5 py-4 rounded-xl text-sm mb-6 flex items-center gap-2">
                <i data-lucide="info" class="w-5 h-5 shrink-0"></i>
                Este projeto não está mais recebendo propostas (status atual: <strong>{{ $project->status }}</strong>). As propostas abaixo são apenas informativas.
            </div>
        @endif

        @if ($proposals->isEmpty())
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <i data-lucide="file-text" class="w-12 h-12 text-gray-300 mx-auto mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900">Nenhuma proposta encontrada</h3>
                <p class="text-gray-500">
                    {{ $filter !== 'all' ? 'Nenhuma proposta com este filtro.' : 'Este projeto ainda não recebeu propostas.' }}
                </p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($proposals as $proposal)
                    @php
                        $statusConfig = [
                            'pendente' => ['label' => 'Pendente', 'class' => 'bg-amber-100 text-amber-700', 'icon' => 'clock'],
                            'aceita' => ['label' => 'Aceita', 'class' => 'bg-green-100 text-green-700', 'icon' => 'check-circle'],
                            'rejeitada' => ['label' => 'Rejeitada', 'class' => 'bg-red-100 text-red-700', 'icon' => 'x-circle'],
                            'retirada' => ['label' => 'Retirada', 'class' => 'bg-gray-100 text-gray-600', 'icon' => 'undo-2'],
                        ][$proposal->status] ?? ['label' => ucfirst($proposal->status), 'class' => 'bg-gray-100 text-gray-600', 'icon' => 'circle'];

                        $canDecide = $proposal->status === 'pendente' && $project->status === 'aguardando_propostas';
                    @endphp
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden p-6">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4 mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <img src="{{ $proposal->freelancer->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($proposal->freelancer->name).'&background=random&size=96' }}"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-100 shrink-0" alt="Freelancer">
                                <div class="min-w-0">
                                    <a href="{{ route('freelancers.show', $proposal->freelancer) }}" class="font-bold text-gray-900 hover:text-green-600">
                                        {{ $proposal->freelancer->name }}
                                    </a>
                                    <p class="text-sm text-gray-600 truncate">{{ $proposal->freelancer->profile?->professional_title ?? 'Freelancer' }}</p>
                                    <p class="text-xs text-gray-500 flex flex-wrap items-center gap-x-3 gap-y-0.5 mt-1">
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400"></i>
                                            {{ number_format((float) ($proposal->freelancer->rating_avg ?? 0), 1, ',', '.') }} ({{ $proposal->freelancer->reviews_received_count ?? 0 }})
                                        </span>
                                        <span class="inline-flex items-center gap-1">
                                            <i data-lucide="briefcase" class="w-3.5 h-3.5"></i>
                                            {{ $proposal->freelancer->profile?->projects_completed ?? 0 }} projetos
                                        </span>
                                        <span>{{ $proposal->created_at->format('d/m/Y H:i') }}</span>
                                    </p>
                                </div>
                            </div>

                            <div class="flex md:flex-col items-center md:items-end gap-2 shrink-0">
                                <span class="text-lg font-bold text-green-600">R$ {{ number_format((float) $proposal->offer, 2, ',', '.') }}</span>
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['class'] }}">
                                    <i data-lucide="{{ $statusConfig['icon'] }}" class="w-3 h-3"></i> {{ $statusConfig['label'] }}
                                </span>
                            </div>
                        </div>

                        @if ($proposal->freelancer->skills->isNotEmpty())
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach ($proposal->freelancer->skills->take(4) as $skill)
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600">{{ $skill->name }}</span>
                                @endforeach
                                @if ($proposal->freelancer->skills->count() > 4)
                                    <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-50 text-gray-400">+{{ $proposal->freelancer->skills->count() - 4 }}</span>
                                @endif
                            </div>
                        @endif

                        <div class="flex items-center gap-2 text-sm mb-3">
                            <span class="text-gray-500">Prazo estimado:</span>
                            <span class="font-semibold text-gray-900">{{ $proposal->estimated_days }} dias</span>
                        </div>

                        <p class="text-sm text-gray-700 leading-relaxed mb-6">{{ $proposal->details }}</p>

                        @if ($canDecide)
                            <div class="flex flex-col sm:flex-row gap-3">
                                <form method="POST" action="{{ route('proposals.accept', [$project, $proposal]) }}"
                                      class="flex-1"
                                      onsubmit="return confirm('Aceitar esta proposta de R$ {{ number_format((float) $proposal->offer, 2, ',', '.') }}? As demais propostas pendentes serão rejeitadas.')">
                                    @csrf
                                    <button class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2">
                                        <i data-lucide="check" class="w-4 h-4"></i> Aceitar proposta
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('proposals.reject', [$project, $proposal]) }}" class="flex-1">
                                    @csrf
                                    <button class="w-full py-3 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl font-semibold flex items-center justify-center gap-2">
                                        <i data-lucide="x" class="w-4 h-4"></i> Rejeitar
                                    </button>
                                </form>
                            </div>
                        @elseif ($proposal->status === 'aceita')
                            <div class="bg-green-50 p-4 rounded-xl border border-green-100 text-sm text-green-700 flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i>
                                Proposta aceita — aguardando pagamento em custódia.
                            </div>
                        @elseif ($proposal->status === 'pendente')
                            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 text-sm text-gray-600 flex items-center gap-2">
                                <i data-lucide="info" class="w-4 h-4"></i>
                                Proposta pendente, mas o projeto já não está recebendo propostas.
                            </div>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</x-layout>
