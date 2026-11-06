<?php use Illuminate\Support\Str; ?>

<x-layout title="Pagamentos - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Todos os Pagamentos</h1>
                <p class="text-gray-600">Gerencie todos os pagamentos da plataforma.</p>
            </div>
        </div>

        <!-- Cards de resumo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm font-semibold text-gray-500 uppercase">Total em Custódia</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">R$ 0,00</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm font-semibold text-gray-500 uppercase">Pagamentos Hoje</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">0</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm font-semibold text-gray-500 uppercase">Taxa Plataforma (Hoje)</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">R$ 0,00</p>
            </div>
        </div>

        <!-- Tabela de pagamentos -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="relative">
                        <input type="text" placeholder="Buscar pagamentos..."
                               class="w-full px-4 py-2.5 pl-10 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                        <div class="absolute left-3 top-1/2 -mt-2.5">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                            <option>Todos os métodos</option>
                            <option>Pix</option>
                            <option>Cartão</option>
                            <option>Boleto</option>
                        </select>
                        <select class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                            <option>Todos os status</option>
                            <option>Pendente</option>
                            <option>Custódia</option>
                            <option>Liberado</option>
                            <option>Estornado</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">ID</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Freelancer</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Projeto</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Valor</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Taxa Plataforma</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Método</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Data</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if ($payments->isEmpty())
                            <tr>
                                <td colspan="10" class="p-8 text-center text-gray-500">
                                    Nenhum pagamento encontrado.
                                </td>
                            </tr>
                        @else
                            @foreach ($payments as $payment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-mono text-gray-600">#{{ $payment->id }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $payment->client->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($payment->client->name) . '&background=random' }}"
                                                 class="w-8 h-8 rounded-full object-cover border-2 border-gray-100" alt="Cliente">
                                            <span class="text-sm text-gray-900">{{ $payment->client->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-700">{{ $payment->freelancer->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">{{ Str::limit($payment->project->title, 30) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600">R$ {{ number_format($payment->platform_fee, 2, ',', '.') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600 capitalize">{{ $payment->method }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if ($payment->status === 'pendente') bg-amber-100 text-amber-700
                                            @elseif ($payment->status === 'custodia') bg-blue-100 text-blue-700
                                            @elseif ($payment->status === 'liberado') bg-green-100 text-green-700
                                            @elseif ($payment->status === 'estornado') bg-red-100 text-red-700
                                            @endif">
                                            {{ str_replace('_', ' ', $payment->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-500">{{ $payment->created_at->format('d/m/Y H:i') }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('admin.payments.show', $payment) }}"
                                           class="text-sm text-green-600 hover:text-green-700 font-medium">
                                            Ver Detalhes →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

</x-layout>
