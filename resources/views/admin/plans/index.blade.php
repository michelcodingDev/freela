<?php use Illuminate\Support\Str; ?>

<x-layout title="Planos - Admin - FreelaJob">

    <div class="max-w-7xl mx-auto py-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Gerenciar Planos</h1>
                <p class="text-gray-600">Configure os planos de assinatura da plataforma.</p>
            </div>
            <a href="{{ route('admin.plans.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2 transition-all shadow-lg">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Novo Plano
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($plans as $plan)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100
                    {{ $plan->is_featured ? 'ring-2 ring-green-500' : '' }}
                    hover:shadow-md transition-all group">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">{{ $plan->name }}</h3>
                                <span class="text-xs font-medium
                                    {{ $plan->is_featured ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}
                                    px-2 py-1 rounded">
                                    {{ $plan->is_featured ? 'Destaque' : 'Padrão' }}
                                </span>
                            </div>
                            <span class="text-xs font-semibold
                                {{ $plan->is_active ? 'text-green-600' : 'text-red-600' }}">
                                {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>

                        <p class="text-gray-600 text-sm mb-6 line-clamp-2">{{ $plan->description }}</p>

                        <div class="mb-6">
                            <p class="text-3xl font-bold text-gray-900">R$ {{ number_format($plan->price_monthly, 2, ',', '.') }}<span class="text-lg font-normal text-gray-500">/mês</span></p>
                            <p class="text-xs text-gray-500">Taxa: {{ $plan->commission_percentage }}% (mín. R$ {{ number_format($plan->min_commission, 2, ',', '.') }})</p>
                        </div>

                        <div class="space-y-3 mb-6">
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <i data-lucide="file-text" class="w-4 h-4 text-gray-400"></i>
                                <span>{{ $plan->proposals_per_month }} propostas/mês</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <i data-lucide="image" class="w-4 h-4 text-gray-400"></i>
                                <span>{{ $plan->max_portfolio_items }} itens portfólio</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-700">
                                <i data-lucide="clock" class="w-4 h-4 text-gray-400"></i>
                                <span>Saque em {{ $plan->withdrawal_days }} dias úteis</span>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ route('admin.plans.edit', $plan) }}"
                               class="flex-1 py-2.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg text-center text-sm font-medium transition-colors">
                                Editar
                            </a>
                            <button onclick="toggleStatus({{ $plan->id }})"
                                    class="flex-1 py-2.5
                                        {{ $plan->is_active ? 'bg-red-50 hover:bg-red-100 text-red-700' : 'bg-green-50 hover:bg-green-100 text-green-700' }}
                                        rounded-lg text-sm font-medium transition-colors border
                                        {{ $plan->is_active ? 'border-red-100' : 'border-green-100' }}">
                                {{ $plan->is_active ? 'Desativar' : 'Ativar' }}
                            </button>
                        </div>

                        <form method="POST" action="{{ route('admin.plans.destroy', $plan) }}" class="mt-4 hidden" id="delete-form-{{ $plan->id }}">
                            @csrf
                            @method('DELETE')
                        </form>

                        <button onclick="confirmDelete({{ $plan->id }})"
                                class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-600 rounded-lg text-sm font-medium transition-colors">
                            Excluir
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

    <script>
        function confirmDelete(id) {
            if (confirm('Tem certeza que deseja excluir este plano?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }

        function toggleStatus(id) {
            fetch('/admin/plans/' + id + '/toggle', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ is_active: !document.querySelector('#plan-' + id + ' .status-badge').classList.contains('text-green-600') }
            }).then(response => response.json())
              .then(data => {
                  if (data.success) location.reload();
              });
        }
    </script>

</x-layout>
