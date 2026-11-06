<x-layout title="Plano - {{ $plan->name }} - Admin - FreelaJob">

    <div class="max-w-3xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h1>
                <p class="text-gray-600 text-sm">{{ $plan->description }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.plans.edit', $plan) }}"
                   class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all">
                    Editar
                </a>
                <a href="{{ route('admin.plans.index') }}"
                   class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all">
                    Voltar
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informações principais -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Dados do Plano</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Nome</span>
                            <span class="text-sm font-medium text-gray-900">{{ $plan->name }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Slug</span>
                            <span class="font-mono text-sm text-gray-700">{{ $plan->slug }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Preço Mensal</span>
                            <span class="text-sm font-bold text-gray-900">R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Comissão da Plataforma</span>
                            <span class="text-sm font-bold text-gray-900">{{ $plan->commission_percentage }}%</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Comissão Mínima</span>
                            <span class="text-sm font-bold text-gray-900">R$ {{ number_format($plan->min_commission, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Propostas por Mês</span>
                            <span class="text-sm font-medium text-gray-900">{{ $plan->proposals_per_month }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Itens no Portfólio</span>
                            <span class="text-sm font-medium text-gray-900">{{ $plan->max_portfolio_items }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Dias para Saque</span>
                            <span class="text-sm font-medium text-gray-900">{{ $plan->withdrawal_days }} dias úteis</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Status</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $plan->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Plano em Destaque</span>
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $plan->is_featured ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $plan->is_featured ? 'Sim' : 'Não' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Ordem de Exibição</span>
                            <span class="text-sm text-gray-700">{{ $plan->sort_order }}</span>
                        </div>
                    </div>
                </div>

                <!-- Funcionalidades -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Funcionalidades</h3>

                    @if ($plan->features->isEmpty())
                        <p class="text-gray-500 text-center py-8">Nenhuma funcionalidade cadastrada.</p>
                    @else
                        <div class="space-y-3">
                            @foreach ($plan->features as $feature)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <i data-lucide="{{ $feature->is_included ? 'check-circle' : 'minus-circle' }}"
                                           class="w-4 h-4 {{ $feature->is_included ? 'text-green-600' : 'text-gray-400' }}"></i>
                                        <span class="text-sm text-gray-700 font-medium">{{ $feature->name }}</span>
                                    </div>
                                    <span class="text-xs text-gray-500">{{ $feature->description }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-64 flex-shrink-0 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Ações</h3>

                    <div class="space-y-3">
                        <a href="{{ route('admin.plans.edit', $plan) }}"
                           class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-xl text-blue-700 font-medium transition-all border border-blue-100">
                            <i data-lucide="edit-2" class="w-4 h-4"></i>
                            Editar Plano
                        </a>

                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" onsubmit="return confirm('Tem certeza que deseja excluir este plano?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="w-full flex items-center gap-3 px-4 py-3 bg-red-50 hover:bg-red-100 rounded-xl text-red-700 font-medium transition-all border border-red-100">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                Excluir Plano
                            </button>
                        </form>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Assinantes</h3>

                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100">
                        <span class="text-sm text-gray-500">Total de assinantes</span>
                        <span class="text-sm font-bold text-gray-900">{{ $plan->subscriptions()->where('status', 'ativo')->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layout>
