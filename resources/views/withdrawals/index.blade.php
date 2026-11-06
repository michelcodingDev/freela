<x-layout title="Saques - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Meus Saques</h1>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 mb-6">
        <p class="text-sm text-gray-500">Saldo disponível</p>
        <p class="text-3xl font-bold text-gray-900">R$ {{ number_format($available, 2, ',', '.') }}</p>
        <p class="text-xs text-gray-400 mt-1">Taxa Pix 2% • Mínimo R$ 10,00</p>

        <form method="POST" action="{{ route('withdrawals.store') }}" class="mt-6 grid grid-cols-1 sm:grid-cols-3 gap-3">
            @csrf
            <input type="number" name="amount" step="0.01" min="10" required placeholder="Valor R$" class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm">
            <input type="text" name="pix_key" required maxlength="100" placeholder="Chave Pix" class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm">
            <button class="py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">Solicitar saque</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h2 class="font-bold text-gray-900 mb-4">Histórico</h2>
        @forelse ($withdrawals as $w)
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div>
                    <p class="text-sm font-medium text-gray-900">R$ {{ number_format($w->amount, 2, ',', '.') }} → líquido R$ {{ number_format($w->net_amount, 2, ',', '.') }}</p>
                    <p class="text-xs text-gray-500">{{ $w->pix_key }} • {{ $w->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 capitalize">{{ $w->status }}</span>
            </div>
        @empty
            <p class="text-sm text-gray-400">Nenhum saque solicitado.</p>
        @endforelse
        <div class="mt-4">{{ $withdrawals->links() }}</div>
    </div>
</div>
</x-layout>
