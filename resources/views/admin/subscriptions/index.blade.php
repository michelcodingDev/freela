<?php use Illuminate\Support\Str; ?>

<x-layout title="Assinaturas - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Assinaturas de Usuários</h1>
                <p class="text-gray-600">Gerencie todas as assinaturas de planos da plataforma.</p>
            </div>
        </div>

        <!-- Cards de resumo -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm font-semibold text-gray-500 uppercase">Total de Assinaturas</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $subscriptions->total() }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm font-semibold text-gray-500 uppercase">Assinaturas Ativas</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">{{ $subscriptions->where('status', 'ativo')->count() }}</p>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm font-semibold text-gray-500 uppercase">Receita Mensal</p>
                <p class="text-2xl font-bold text-gray-900 mt-1">R$ 0,00</p>
            </div>
        </div>

        <!-- Tabela de assinaturas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="relative">
                    <input type="text" placeholder="Buscar usuários ou planos..."
                           class="w-full px-4 py-2.5 pl-10 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                    <div class="absolute left-3 top-1/2 -mt-2.5">
                        <i data-lucide="search" class="w-4 h-4 text-gray-400"></i>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Usuário</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Plano</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Valor Pago</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Início</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Término</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Método</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($subscriptions as $subscription)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $subscription->user->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($subscription->user->name) . '&background=random' }}"
                                             class="w-8 h-8 rounded-full object-cover border-2 border-gray-100" alt="Usuário">
                                        <span class="text-sm text-gray-900">{{ $subscription->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-medium text-gray-700">{{ $subscription->plan->name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">R$ {{ number_format($subscription->price_paid, 2, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500">{{ $subscription->starts_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-500">{{ $subscription->ends_at->format('d/m/Y') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if ($subscription->status === 'ativo') bg-green-100 text-green-700
                                        @elseif ($subscription->status === 'inativo') bg-gray-100 text-gray-600
                                        @elseif ($subscription->status === 'cancelado') bg-red-100 text-red-700
                                        @elseif ($subscription->status === 'expirado') bg-amber-100 text-amber-700
                                        @endif">
                                        {{ str_replace('_', ' ', $subscription->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600 capitalize">{{ $subscription->payment_method }}</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $subscriptions->links() }}
            </div>
        </div>
    </div>

</x-layout>
