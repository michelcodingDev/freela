<x-layout title="Editar Plano - {{ $plan->name }} - FreelaJob">

    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Plano</h1>
                    <p class="text-gray-600 text-sm">{{ $plan->name }}</p>
                </div>
                <a href="{{ route('admin.plans.index') }}"
                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                    ← Voltar aos planos
                </a>
            </div>

            <form method="POST" action="{{ route('admin.plans.update', $plan) }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Plano *</label>
                    <input type="text" name="name" id="name"
                           value="{{ old('name', $plan->name) }}"
                           required
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Descrição *</label>
                    <textarea name="description" id="description" rows="4" required
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">{{ old('description', $plan->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="price_monthly" class="block text-sm font-medium text-gray-700 mb-1">Preço Mensal (R$) *</label>
                        <input type="number" name="price_monthly" id="price_monthly"
                               value="{{ old('price_monthly', $plan->price_monthly) }}"
                               required
                               step="0.01" min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('price_monthly')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="commission_percentage" class="block text-sm font-medium text-gray-700 mb-1">Comissão da Plataforma (%) *</label>
                        <input type="number" name="commission_percentage" id="commission_percentage"
                               value="{{ old('commission_percentage', $plan->commission_percentage) }}"
                               required
                               step="0.01" min="0" max="100"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('commission_percentage')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="min_commission" class="block text-sm font-medium text-gray-700 mb-1">Comissão Mínima (R$) *</label>
                        <input type="number" name="min_commission" id="min_commission"
                               value="{{ old('min_commission', $plan->min_commission) }}"
                               required
                               step="0.01" min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('min_commission')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="proposals_per_month" class="block text-sm font-medium text-gray-700 mb-1">Propostas por Mês *</label>
                        <input type="number" name="proposals_per_month" id="proposals_per_month"
                               value="{{ old('proposals_per_month', $plan->proposals_per_month) }}"
                               required
                               min="1"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('proposals_per_month')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="max_portfolio_items" class="block text-sm font-medium text-gray-700 mb-1">Itens no Portfólio *</label>
                        <input type="number" name="max_portfolio_items" id="max_portfolio_items"
                               value="{{ old('max_portfolio_items', $plan->max_portfolio_items) }}"
                               required
                               min="1"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('max_portfolio_items')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="withdrawal_days" class="block text-sm font-medium text-gray-700 mb-1">Dias para Saque *</label>
                        <input type="number" name="withdrawal_days" id="withdrawal_days"
                               value="{{ old('withdrawal_days', $plan->withdrawal_days) }}"
                               required
                               min="1"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('withdrawal_days')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Funcionalidades</h3>
                        <button type="button" onclick="addFeature()"
                                class="text-xs text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                            <i data-lucide="plus" class="w-3 h-3"></i>
                            Adicionar funcionalidade
                        </button>
                    </div>

                    <div id="features-container" class="space-y-3">
                        @php $featureIndex = 0; @endphp
                        @foreach ($plan->features as $feature)
                            <div class="feature-row flex gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <input type="hidden" name="features[{{ $featureIndex }}][name]" value="{{ old('features.' . $featureIndex . '.name', $feature->name) }}">
                                <input type="text" name="features[{{ $featureIndex }}][name]"
                                       value="{{ old('features.' . $featureIndex . '.name', $feature->name) }}"
                                       placeholder="Nome da funcionalidade"
                                       class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500">

                                <input type="text" name="features[{{ $featureIndex }}][description]"
                                       value="{{ old('features.' . $featureIndex . '.description', $feature->description) }}"
                                       placeholder="Descrição"
                                       class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500">

                                <label class="flex items-center gap-2">
                                    <input type="checkbox" name="features[{{ $featureIndex }}][is_included]" value="1"
                                           {{ old('features.' . $featureIndex . '.is_included', $feature->is_included) ? 'checked' : '' }}>
                                    <span class="text-xs text-gray-600">Incluído</span>
                                </label>

                                <button type="button" onclick="removeFeature(this)"
                                        class="text-gray-400 hover:text-red-500">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </div>
                            @php $featureIndex++; @endphp
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Ordem de Exibição</label>
                        <input type="number" name="sort_order" id="sort_order"
                               value="{{ old('sort_order', $plan->sort_order) }}"
                               min="0"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                    </div>

                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ $plan->is_featured ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="text-sm text-gray-700">Plano em destaque</span>
                        </label>
                    </div>

                    <div class="flex items-end">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                   {{ $plan->is_active ? 'checked' : '' }}
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <span class="text-sm text-gray-700">Plano ativo</span>
                        </label>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all shadow-lg transform active:scale-95">
                        Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let featureCount = {{ $plan->features->count() }};

        function addFeature() {
            const container = document.getElementById('features-container');
            const row = document.createElement('div');
            row.className = 'feature-row flex gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200';
            row.innerHTML = `
                <input type="hidden" name="features[${featureCount}][name]" value="">
                <input type="text" name="features[${featureCount}][name]" placeholder="Nome da funcionalidade"
                       class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500">
                <input type="text" name="features[${featureCount}][description]" placeholder="Descrição"
                       class="flex-1 px-3 py-2 bg-white border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="features[${featureCount}][is_included]" value="1" checked>
                    <span class="text-xs text-gray-600">Incluído</span>
                </label>
                <button type="button" onclick="removeFeature(this)" class="text-gray-400 hover:text-red-500">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            `;
            container.appendChild(row);
            featureCount++;
            lucide.createIcons();
        }

        function removeFeature(button) {
            button.closest('.feature-row').remove();
        }
    </script>

</x-layout>
