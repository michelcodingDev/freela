<x-layout title="Carteira - FreelaJob">
    @php
        $isFreelancer = $user->isFreelancer();
        $statusColors = [
            'pendente' => 'bg-amber-100 text-amber-700',
            'custodia' => 'bg-blue-100 text-blue-700',
            'liberado' => 'bg-green-100 text-green-700',
            'estornado' => 'bg-red-100 text-red-700',
            'solicitado' => 'bg-amber-100 text-amber-700',
            'processando' => 'bg-blue-100 text-blue-700',
            'pago' => 'bg-green-100 text-green-700',
            'cancelado' => 'bg-gray-100 text-gray-600',
        ];
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Carteira e Extrato</h1>
            <p class="text-gray-600">Acompanhe seus recebimentos, custódias e saques.</p>
        </div>
        <a href="{{ route('wallet.export') }}"
           class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 rounded-xl font-semibold text-sm shadow-sm">
            <i data-lucide="download" class="w-4 h-4"></i> Exportar CSV
        </a>
    </div>

    @if ($isFreelancer)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Saldo disponível</p>
                <h3 class="text-2xl font-bold text-green-600 mt-1">R$ {{ number_format($summary['available'], 2, ',', '.') }}</h3>
                <a href="{{ route('withdrawals.index') }}" class="text-xs text-green-700 font-medium hover:underline">Solicitar saque</a>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Em custódia</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">R$ {{ number_format($summary['locked'], 2, ',', '.') }}</h3>
                <p class="text-xs text-gray-400">Liberado após a entrega</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Total recebido (líquido)</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($summary['total_earned'], 2, ',', '.') }}</h3>
                <p class="text-xs text-gray-400">Bruto R$ {{ number_format($summary['gross_released'], 2, ',', '.') }} • taxas R$ {{ number_format($summary['platform_fees'], 2, ',', '.') }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Saques pagos</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($summary['withdrawals_paid'], 2, ',', '.') }}</h3>
                <p class="text-xs text-gray-400">Pendentes R$ {{ number_format($summary['withdrawals_pending'], 2, ',', '.') }}</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Em custódia</p>
                <h3 class="text-2xl font-bold text-blue-600 mt-1">R$ {{ number_format($summary['in_escrow'], 2, ',', '.') }}</h3>
                <p class="text-xs text-gray-400">Protegido até a entrega</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Total pago</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($summary['total_paid'], 2, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Liberado a freelancers</p>
                <h3 class="text-2xl font-bold text-green-600 mt-1">R$ {{ number_format($summary['released'], 2, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Estornado</p>
                <h3 class="text-2xl font-bold text-red-600 mt-1">R$ {{ number_format($summary['refunded'], 2, ',', '.') }}</h3>
                <p class="text-xs text-gray-400">{{ $summary['active_payments'] }} pagamento(s) em andamento</p>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-900">Extrato</h2>
            <span class="text-xs text-gray-500">{{ $totalTransactions }} lançamento(s)</span>
        </div>

        @if ($transactions->isEmpty())
            <div class="p-10 text-center">
                <i data-lucide="receipt" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                <p class="text-gray-500">Nenhum lançamento financeiro ainda.</p>
                <p class="text-sm text-gray-400 mt-1">
                    {{ $isFreelancer ? 'Seus recebimentos aparecerão aqui após o primeiro projeto.' : 'Seus pagamentos aparecerão aqui após contratar um freelancer.' }}
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wide">
                        <tr>
                            <th class="text-left font-semibold px-6 py-3">Data</th>
                            <th class="text-left font-semibold px-6 py-3">Descrição</th>
                            <th class="text-left font-semibold px-6 py-3">Status</th>
                            <th class="text-right font-semibold px-6 py-3">Valor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($transactions as $transaction)
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-6 py-4 text-gray-500 whitespace-nowrap">{{ $transaction['date']->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4">
                                    @if ($transaction['url'])
                                        <a href="{{ $transaction['url'] }}" class="font-medium text-gray-900 hover:text-green-600">{{ $transaction['description'] }}</a>
                                    @else
                                        <span class="font-medium text-gray-900">{{ $transaction['description'] }}</span>
                                    @endif
                                    <p class="text-xs text-gray-400">{{ $transaction['direction'] === 'credito' ? 'Crédito' : 'Débito' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$transaction['status']] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $transaction['status_label'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold whitespace-nowrap {{ $transaction['direction'] === 'credito' ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ $transaction['direction'] === 'credito' ? '+' : '−' }} R$ {{ number_format($transaction['amount'], 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</x-layout>
