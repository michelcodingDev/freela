<x-layout title="Saques - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Meus saques</h1>
        <p class="text-gray-600 text-sm">Saque seu saldo via Pix. Processamento em até {{ $summary['days'] ?? 4 }} dia(s) útil(eis) conforme seu plano.</p>
    </div>

    <!-- Resumo -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 font-medium">Saldo disponível</p>
            <p class="text-2xl font-bold text-green-600 mt-1">R$ {{ number_format($available, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Taxa Pix 2% · Mínimo R$ 10,00</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 font-medium">Em processamento</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">R$ {{ number_format($summary['pending'] ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Saques pendentes (líquido)</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs text-gray-500 font-medium">Total pago</p>
            <p class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($summary['paid'] ?? 0, 2, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">Saques pagos via Pix</p>
        </div>
    </div>

    <!-- Novo saque -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6">
        <h2 class="font-bold text-gray-900">Solicitar saque</h2>
        <form method="POST" action="{{ route('withdrawals.store') }}" class="mt-4 space-y-4" x-data="{ amount: 0 }">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1.5">Valor (R$) *</label>
                    <input id="amount" type="number" name="amount" step="0.01" min="10" max="{{ $available }}" required x-model.number="amount"
                           placeholder="Mínimo R$ 10,00"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm">
                    @error('amount')
                        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="pix_key" class="block text-sm font-medium text-gray-700 mb-1.5">Chave Pix *</label>
                    <input id="pix_key" type="text" name="pix_key" required maxlength="100" value="{{ old('pix_key', auth()->user()->profile?->pix_key) }}"
                           placeholder="CPF, e-mail, telefone ou aleatória"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm">
                    @error('pix_key')
                        <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-sm" x-show="amount > 0" x-cloak>
                <div class="flex flex-wrap justify-between gap-2 text-green-900">
                    <span>Você receberá:</span>
                    <strong x-text="'R$ ' + (amount * 0.98).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></strong>
                </div>
                <p class="text-xs text-green-700 mt-1">Taxa Pix de 2% (R$ <span x-text="(amount * 0.02).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></span>) incluída. Previsão: até {{ $summary['days'] ?? 4 }} dia(s) útil(eis).</p>
            </div>

            <button class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2"
                    @if($available < 10) disabled @endif>
                <i data-lucide="wallet" class="w-5 h-5"></i>
                {{ $available >= 10 ? 'Solicitar saque' : 'Saldo insuficiente para saque' }}
            </button>
        </form>
    </div>

    <!-- Histórico -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6">
        <h2 class="font-bold text-gray-900 mb-2">Histórico</h2>
        <div class="divide-y divide-gray-100">
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
                <div class="flex flex-wrap items-center justify-between gap-3 py-4">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900">
                            R$ {{ number_format((float) $w->amount, 2, ',', '.') }}
                            <span class="font-normal text-gray-500">· líquido R$ {{ number_format((float) $w->net_amount, 2, ',', '.') }}</span>
                        </p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Pix {{ \App\Support\Mask::pixKey($w->pix_key) }}
                            · solicitado em {{ $w->created_at->format('d/m/Y H:i') }}
                            @if ($w->paid_at)
                                · pago em {{ $w->paid_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize {{ $badge }}">{{ $w->status }}</span>
                        @if (in_array($w->status, ['solicitado', 'processando'], true))
                            <form method="POST" action="{{ route('withdrawals.cancel', $w) }}"
                                  onsubmit="return confirm('Cancelar este saque? O valor voltará para seu saldo.')">
                                @csrf
                                <button class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium">Cancelar</button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-10 text-center">
                    <i data-lucide="wallet" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                    <p class="text-gray-500">Nenhum saque solicitado.</p>
                    <p class="text-sm text-gray-400 mt-1">Seu saldo disponível aparece aqui após pagamentos liberados.</p>
                </div>
            @endforelse
        </div>
        <div class="mt-4">{{ $withdrawals->links() }}</div>
    </div>
</div>
</x-layout>
