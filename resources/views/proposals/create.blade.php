<x-layout title="Enviar Proposta - FreelaJob">

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Enviar Proposta</h1>
                    <p class="text-gray-600 text-sm">{{ $project->title }}</p>
                </div>
                <a href="{{ route('projects.show', $project) }}"
                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                    ← Voltar ao projeto
                </a>
            </div>

            @if (! empty($usage))
                <div class="rounded-xl border px-5 py-4 mb-6 flex flex-wrap items-center justify-between gap-3 {{ $usage['atLimit'] ? 'bg-red-50 border-red-200 text-red-800' : 'bg-blue-50 border-blue-200 text-blue-800' }}">
                    <p class="text-sm">
                        <i data-lucide="send" class="w-4 h-4 inline mr-1"></i>
                        Plano <strong>{{ $usage['plan'] }}</strong>: {{ $usage['used'] }}/{{ $usage['limit'] }}
                        propostas enviadas este mês
                        @if ($usage['atLimit'])
                            — limite atingido.
                        @else
                            (faltam {{ $usage['remaining'] }}).
                        @endif
                    </p>
                    @if ($usage['atLimit'])
                        <a href="{{ route('subscriptions.index') }}" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold shrink-0">
                            Ver planos
                        </a>
                    @endif
                </div>
            @endif

            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 mb-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Informações do Projeto</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">Título</span>
                        <span class="text-sm text-gray-900 font-medium">{{ $project->title }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">Categoria</span>
                        <span class="text-sm text-gray-700">{{ $project->category->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">Cliente</span>
                        <span class="text-sm text-gray-700">{{ $project->client->name }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">Nível</span>
                        <span class="text-sm text-gray-700 capitalize">{{ $project->experience_level }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600 text-sm">Prazo para propostas</span>
                        <span class="text-sm text-gray-700">{{ $project->proposal_deadline->format('d/m/Y') }}</span>
                    </div>
                    @if ($project->budget_min && $project->budget_max)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Orçamento</span>
                            <span class="text-sm font-semibold text-gray-900">
                                R$ {{ number_format($project->budget_min, 2, ',', '.') }} - R$ {{ number_format($project->budget_max, 2, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            @php
                $commissionPercentage = $commission['percentage'] ?? 20;
                $commissionMin = $commission['min'] ?? 5;
            @endphp

            <form method="POST" action="{{ route('proposals.store', $project) }}" class="space-y-6"
                  x-data="{ offer: {{ old('offer', 0) }}, commissionPct: {{ $commissionPercentage }}, commissionMin: {{ $commissionMin }} }">
                @csrf

                <div>
                    <label for="offer" class="block text-sm font-medium text-gray-700 mb-1">Sua Oferta (R$) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-2.5 text-gray-400">R$</span>
                        <input type="number" name="offer" id="offer"
                               value="{{ old('offer') }}"
                               x-model.number="offer"
                               required
                               step="0.01" min="1"
                               class="w-full px-4 py-2.5 pl-10 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="0,00">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Este é o valor que você deseja receber pelo projeto.</p>
                    @error('offer')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-green-50 border border-green-100 rounded-xl p-4 text-sm" x-show="offer > 0" x-cloak>
                    <p class="font-semibold text-green-800">Estimativa de recebimento</p>
                    <div class="grid sm:grid-cols-3 gap-3 mt-2 text-green-900">
                        <div>
                            <p class="text-xs text-green-700">Taxa da plataforma ({{ $commissionPercentage }}%)</p>
                            <p class="font-bold" x-text="'R$ ' + Math.max(offer * commissionPct / 100, commissionMin).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700">Você receberia (antes do gateway)</p>
                            <p class="font-bold" x-text="'R$ ' + (offer - Math.max(offer * commissionPct / 100, commissionMin)).toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })"></p>
                        </div>
                        <div>
                            <p class="text-xs text-green-700">Taxa do gateway</p>
                            <p class="font-medium text-xs mt-1">Calculada no pagamento (Pix, cartão ou boleto).</p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="estimated_days" class="block text-sm font-medium text-gray-700 mb-1">Prazo Estimado (dias) *</label>
                    <input type="number" name="estimated_days" id="estimated_days"
                           value="{{ old('estimated_days', 7) }}"
                           required
                           min="1" max="365"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                    <p class="text-xs text-gray-500 mt-1">Quantos dias você precisa para concluir o projeto.</p>
                    @error('estimated_days')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="details" class="block text-sm font-medium text-gray-700 mb-1">Detalhes da Proposta *</label>
                    <textarea name="details" id="details" rows="6" required
                              class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                              placeholder="Descreva como você pretende executar o projeto, sua experiência relevante, tecnologias que usará, entregáveis e qualquer diferencial.">{{ old('details') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Mínimo de 50 caracteres. Seja detalhado para aumentar suas chances de ser contratado.</p>
                    @error('details')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-all shadow-lg transform active:scale-95 flex items-center gap-2">
                        <i data-lucide="send" class="w-5 h-5"></i>
                        Enviar Proposta
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layout>
