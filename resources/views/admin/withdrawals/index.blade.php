<x-layout title="Saques Admin - FreelaJob">
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Saques</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-3">
        @forelse ($withdrawals as $w)
            <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-gray-50 rounded-xl border border-gray-100">
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $w->freelancer->name }} • R$ {{ number_format($w->amount, 2, ',', '.') }} → líquido R$ {{ number_format($w->net_amount, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">{{ $w->pix_key }} • {{ $w->created_at->format('d/m/Y H:i') }} • {{ $w->status }}</p>
                </div>
                @if (in_array($w->status, ['solicitado', 'processando']))
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('admin.withdrawals.pay', $w) }}">@csrf<button class="px-3 py-2 bg-green-600 text-white rounded-lg text-xs font-semibold">Pagar</button></form>
                        <form method="POST" action="{{ route('admin.withdrawals.cancel', $w) }}">@csrf<button class="px-3 py-2 bg-red-100 text-red-700 rounded-lg text-xs font-semibold">Cancelar</button></form>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400">Nenhum saque.</p>
        @endforelse
        <div>{{ $withdrawals->links() }}</div>
    </div>
</div>
</x-layout>
