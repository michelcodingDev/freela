<x-layout title="Disputas - FreelaJob">
<div class="max-w-4xl mx-auto py-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Disputas</h1>
            <p class="text-gray-600 text-sm">Acompanhe a análise, o escrow bloqueado e a resolução.</p>
        </div>
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-3 gap-3 mt-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Total</p>
            <p class="text-xl font-bold text-gray-900 mt-1">{{ $summary['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Em análise</p>
            <p class="text-xl font-bold text-amber-600 mt-1">{{ $summary['open'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 text-center">
            <p class="text-xs text-gray-500 font-medium">Resolvidas</p>
            <p class="text-xl font-bold text-green-600 mt-1">{{ $summary['resolved'] }}</p>
        </div>
    </div>

    <!-- Filtro -->
    <div class="flex gap-2 mt-6">
        @foreach (['all' => 'Todas', 'aberta' => 'Em análise', 'resolvida' => 'Resolvidas'] as $value => $label)
            <a href="{{ route('disputes.index', ['status' => $value]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === $value ? 'bg-red-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-red-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($disputes as $d)
            <a href="{{ route('disputes.show', $d) }}"
               class="block bg-white rounded-2xl border shadow-sm p-5 hover:shadow-md transition {{ $d->status === 'aberta' ? 'border-red-200' : 'border-gray-100' }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 hover:text-green-600">{{ $d->project->title }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $d->client->name }} × {{ $d->freelancer->name }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            Aberta em {{ $d->created_at->format('d/m/Y H:i') }} ({{ $d->created_at->diffForHumans() }})
                        </p>
                    </div>
                    <div class="flex flex-col items-end gap-1.5 shrink-0">
                        @if ($d->status === 'aberta')
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                                <i data-lucide="loader" class="w-3 h-3"></i> Em análise
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                <i data-lucide="check-circle" class="w-3 h-3"></i> Resolvida
                            </span>
                            @if ($d->winner)
                                <span class="text-xs text-gray-500 capitalize">Vencedor: {{ $d->winner }}</span>
                            @endif
                        @endif
                    </div>
                </div>
            </a>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <i data-lucide="shield-check" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                <p class="font-medium text-gray-900">
                    {{ $filter === 'aberta' ? 'Nenhuma disputa em análise' : ($filter === 'resolvida' ? 'Nenhuma disputa resolvida' : 'Nenhuma disputa') }}
                </p>
                <p class="text-sm text-gray-500 mt-1">Quando houver uma disputa, ela aparecerá aqui.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $disputes->links() }}</div>
</div>
</x-layout>
