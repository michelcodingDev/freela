<x-layout title="Convites - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Convites recebidos</h1>
        <p class="text-gray-600 text-sm">Clientes que convidaram você para projetos específicos. Pendentes aparecem primeiro.</p>
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-3 gap-3 mt-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Total</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $summary['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Pendentes</p>
            <p class="text-xl font-bold text-amber-600 mt-1">{{ $summary['pending'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Aceitos</p>
            <p class="text-xl font-bold text-green-600 mt-1">{{ $summary['accepted'] }}</p>
        </div>
    </div>

    <!-- Filtro -->
    <div class="flex flex-wrap gap-2 mt-6">
        @foreach (['all' => 'Todos', 'convidado' => 'Pendentes', 'aceito' => 'Aceitos', 'recusado' => 'Recusados'] as $value => $label)
            <a href="{{ route('invites.index', ['status' => $value]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === $value ? 'bg-green-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-green-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($invites as $invite)
            @php
                $statusConfig = [
                    'convidado' => ['label' => 'Pendente', 'class' => 'bg-amber-100 text-amber-700'],
                    'aceito' => ['label' => 'Aceito', 'class' => 'bg-green-100 text-green-700'],
                    'recusado' => ['label' => 'Recusado', 'class' => 'bg-gray-100 text-gray-600'],
                ][$invite->status] ?? ['label' => ucfirst((string) $invite->status), 'class' => 'bg-gray-100 text-gray-600'];
            @endphp
            <article class="bg-white rounded-2xl border shadow-sm p-5 {{ $invite->status === 'convidado' ? 'border-amber-200' : 'border-gray-100' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('projects.show', $invite->project) }}" class="font-bold text-gray-900 hover:text-green-600">
                                {{ $invite->project->title }}
                            </a>
                            <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusConfig['class'] }}">
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1.5">
                            De {{ $invite->client->name }}
                            · {{ $invite->project->category?->name ?? 'Categoria' }}
                            · recebido em {{ $invite->created_at->format('d/m/Y') }}
                        </p>
                        @if ($invite->project->budget_min)
                            <p class="text-sm font-bold text-green-600 mt-2">
                                R$ {{ number_format((float) $invite->project->budget_min, 2, ',', '.') }}
                                @if ($invite->project->budget_max)
                                    <span class="font-normal text-gray-500 text-xs">até R$ {{ number_format((float) $invite->project->budget_max, 2, ',', '.') }}</span>
                                @endif
                                @if ($invite->project->proposal_deadline)
                                    <span class="font-normal text-gray-500 text-xs">
                                        · prazo: {{ $invite->project->proposal_deadline->format('d/m/Y') }}
                                    </span>
                                @endif
                            </p>
                        @endif
                        @if ($invite->message)
                            <div class="bg-gray-50 border border-gray-100 rounded-xl p-3 mt-3">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Mensagem do cliente</p>
                                <p class="text-sm text-gray-700 mt-1">{{ $invite->message }}</p>
                            </div>
                        @endif
                        @if ($invite->accepted_at)
                            <p class="text-xs text-gray-400 mt-2">Aceito em {{ $invite->accepted_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>

                @if ($invite->status === 'convidado')
                    <div class="flex flex-col sm:flex-row gap-2 mt-4">
                        <form method="POST" action="{{ route('invites.accept', $invite) }}" class="sm:flex-1">
                            @csrf
                            <button class="w-full py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold">
                                Aceitar convite
                            </button>
                        </form>
                        <form method="POST" action="{{ route('invites.decline', $invite) }}" class="sm:flex-1">
                            @csrf
                            <button class="w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium">
                                Recusar
                            </button>
                        </form>
                    </div>
                @elseif ($invite->status === 'aceito' && $invite->project->status === 'aguardando_propostas')
                    <div class="flex flex-col sm:flex-row gap-2 mt-4">
                        <a href="{{ route('projects.show', $invite->project) }}"
                           class="flex-1 text-center py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold">
                            Ver projeto
                        </a>
                        <a href="{{ route('proposals.create', $invite->project) }}"
                           class="flex-1 text-center py-2.5 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-xl text-sm font-semibold">
                            Enviar proposta
                        </a>
                    </div>
                @elseif ($invite->status === 'aceito')
                    <a href="{{ route('projects.show', $invite->project) }}"
                       class="inline-flex items-center gap-1.5 mt-4 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-sm font-semibold">
                        Abrir projeto <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
                @endif
            </article>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <i data-lucide="mail-open" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                <p class="font-medium text-gray-900">
                    {{ $filter === 'convidado' ? 'Nenhum convite pendente' : ($filter !== 'all' ? 'Nenhum convite com este filtro' : 'Nenhum convite recebido') }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Quando um cliente convidar você, o convite aparecerá aqui.</p>
                <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">
                    Ver projetos abertos
                </a>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $invites->links() }}</div>
</div>
</x-layout>
