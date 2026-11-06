<x-layout title="Saques Admin - FreelaJob">
<div class="max-w-5xl mx-auto py-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Saques</h1>
            <p class="text-gray-600 text-sm">Processe pagamentos Pix pendentes.</p>
        </div>
        <div class="flex gap-2 text-sm">
            <span class="px-3 py-1.5 rounded-full bg-amber-100 text-amber-700 font-medium">Pendentes: {{ $summary['pending_count'] ?? 0 }} (R$ {{ number_format($summary['pending_amount'] ?? 0, 2, ',', '.') }})</span>
            <span class="px-3 py-1.5 rounded-full bg-green-100 text-green-700 font-medium">Pago: R$ {{ number_format($summary['paid_amount'] ?? 0, 2, ',', '.') }}</span>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mt-6">
        <div class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Freelancer, e-mail, ID ou chave Pix..."
                   class="flex-1 min-w-[220px] px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm">
            <select name="status" class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 outline-none">
                <option value="">Todos os status</option>
                @foreach (['solicitado' => 'Solicitado', 'processando' => 'Processando', 'pago' => 'Pago', 'cancelado' => 'Cancelado'] as $value => $label)
                    <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Filtrar</button>
            @if (request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.withdrawals.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Limpar</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6 space-y-3">
        @forelse ($withdrawals as $w)
            @php
                $badge = match ($w->status) {
                    'solicitado' => 'bg-amber-100 text-amber-700',
                    'processando' => 'bg-blue-100 text-blue-700',
                    'pago' => 'bg-green-100 text-green-700',
                    'cancelado' => 'bg-gray-100 text-gray-600',
                    default => 'bg-gray-100 text-gray-600',
                };
            @endphp
            <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <div class="min-w-0">
                    <p class="font-semibold text-gray-900 text-sm">
                        {{ $w->freelancer->name }}
                        <span class="font-normal text-gray-500">· {{ $w->freelancer->email }}</span>
                    </p>
                    <p class="text-sm text-gray-700 mt-0.5">
                        R$ {{ number_format((float) $w->amount, 2, ',', '.') }}
                        <span class="text-gray-500">· líquido R$ {{ number_format((float) $w->net_amount, 2, ',', '.') }} (taxa R$ {{ number_format((float) $w->fee, 2, ',', '.') }})</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">
                        Pix: <code class="bg-white border border-gray-200 rounded px-1.5 py-0.5">{{ $w->pix_key }}</code>
                        · {{ $w->created_at->format('d/m/Y H:i') }}
                        @if ($w->paid_at)
                            · pago em {{ $w->paid_at->format('d/m/Y H:i') }}
                        @endif
                    </p>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2 shrink-0">
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize text-center {{ $badge }}">{{ $w->status }}</span>
                    @if (in_array($w->status, ['solicitado', 'processando'], true))
                        <form method="POST" action="{{ route('admin.withdrawals.pay', $w) }}"
                              onsubmit="return confirm('Marcar como pago? Isto libera o Pix para o freelancer.')">
                            @csrf
                            <button class="w-full sm:w-auto px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold">Pagar</button>
                        </form>
                        <form method="POST" action="{{ route('admin.withdrawals.cancel', $w) }}"
                              onsubmit="return confirm('Cancelar este saque? O freelancer será notificado.')">
                            @csrf
                            <button class="w-full sm:w-auto px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-semibold">Cancelar</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 py-8 text-center">Nenhum saque encontrado.</p>
        @endforelse
        <div>{{ $withdrawals->links() }}</div>
    </div>
</div>
</x-layout>
