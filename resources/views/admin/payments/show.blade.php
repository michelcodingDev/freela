<x-layout title="Pagamento #{{ $payment->id }} - Admin - FreelaJob">

    <div class="max-w-3xl mx-auto">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pagamento #{{ $payment->id }}</h1>
                <p class="text-gray-600 text-sm">Detalhes da transação</p>
            </div>
            <a href="{{ route('admin.payments') }}"
               class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Voltar aos pagamentos
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Informações principais -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Dados da Transação</h3>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">ID da Transação</span>
                            <span class="font-mono text-sm text-gray-900">{{ $payment->payment_intent_id }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Projeto</span>
                            <span class="text-sm text-gray-900">{{ $payment->project->title }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Método</span>
                            <span class="text-sm text-gray-900 capitalize">{{ $payment->method }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Valor Bruto</span>
                            <span class="text-sm font-bold text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Taxa da Plataforma</span>
                            <span class="text-sm font-bold text-gray-900">R$ {{ number_format($payment->platform_fee, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Taxa do Gateway</span>
                            <span class="text-sm font-bold text-gray-900">R$ {{ number_format($payment->gateway_fee, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Valor Líquido</span>
                            <span class="text-sm font-bold text-gray-900">R$ {{ number_format($payment->net_amount, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Data de Pagamento</span>
                            <span class="text-sm text-gray-600">
                                {{ $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : 'Não pago' }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center p-4 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-gray-600 text-sm">Data de Liberação</span>
                            <span class="text-sm text-gray-600">
                                {{ $payment->released_at ? $payment->released_at->format('d/m/Y H:i') : 'Não liberado' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Alterar Status</h3>

                    <div class="space-y-3">
                        <label class="block text-sm font-medium text-gray-700">Status Atual</label>
                        <select disabled
                              class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-sm cursor-not-allowed">
                            <option>{{ str_replace('_', ' ', $payment->status) }}</option>
                        </select>

                        <div class="flex gap-3">
                            <button class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all">
                                Marcar como Custódia
                            </button>
                            <button class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all">
                                Liberar Pagamento
                            </button>
                            <button class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-all">
                                Estornar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-64 flex-shrink-0 space-y-6">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Usuários Envolvidos</h3>

                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Cliente</p>
                            <div class="flex items-center gap-3">
                                <img src="{{ $payment->client->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($payment->client->name) . '&background=random' }}"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-100" alt="Cliente">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $payment->client->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->client->email }}</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Freelancer</p>
                            <div class="flex items-center gap-3">
                                <img src="{{ $payment->freelancer->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($payment->freelancer->name) . '&background=random' }}"
                                     class="w-12 h-12 rounded-full object-cover border-2 border-gray-100" alt="Freelancer">
                                <div>
                                    <p class="text-sm font-bold text-gray-900">{{ $payment->freelancer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $payment->freelancer->email }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Ações Rápidas</h3>
                    <div class="space-y-3">
                        <a href="{{ route('projects.show', $payment->project) }}"
                           class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-xl text-blue-700 font-medium transition-all border border-blue-100">
                            <i data-lucide="briefcase" class="w-4 h-4"></i>
                            Ver Projeto
                        </a>
                        <a href="{{ route('admin.payments') }}"
                           class="flex items-center gap-3 px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-xl text-gray-700 font-medium transition-all border border-gray-100">
                            <i data-lucide="list" class="w-4 h-4"></i>
                            Lista de Pagamentos
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-layout>
