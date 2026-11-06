<x-layout title="Pagamento - FreelaJob">

    <div class="max-w-4xl mx-auto py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('projects.show', $project) }}"
               class="text-sm text-green-600 hover:text-green-700 font-medium flex items-center gap-1">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Voltar ao projeto
            </a>
            <h1 class="text-2xl font-bold text-gray-900 mt-2">Pagamento em Custódia</h1>
            <p class="text-gray-600 text-sm">{{ $project->title }}</p>
        </div>

        <!-- Cartão de resumo do pagamento -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Resumo do Pagamento</h3>
                    <p class="text-gray-600 text-sm">Valor será retido em custódia até a conclusão do projeto.</p>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <i data-lucide="shield-check" class="w-3 h-3"></i>
                    Seguro
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Valor do Projeto</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Taxa da Plataforma</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($payment->platform_fee, 2, ',', '.') }}</p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl">
                    <p class="text-xs font-semibold text-gray-500 uppercase">Valor Líquido (Freelancer)</p>
                    <p class="text-2xl font-bold text-gray-900">R$ {{ number_format($payment->net_amount, 2, ',', '.') }}</p>
                </div>
            </div>

            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-xl border border-blue-100">
                <div>
                    <p class="text-xs font-semibold text-blue-600 uppercase mb-1">Método de Pagamento</p>
                    <p class="text-sm font-medium text-gray-900 capitalize">{{ $method }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-semibold text-gray-500">ID da Transação</p>
                    <p class="text-sm text-gray-700 font-mono">{{ $payment->payment_intent_id }}</p>
                </div>
            </div>
        </div>

        <!-- Formulário de pagamento por método -->
        @if ($method === 'pix')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Pagamento via Pix</h3>

                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- QR Code -->
                    <div class="lg:w-1/2">
                        <div class="bg-white p-8 rounded-2xl border border-gray-100 text-center">
                            <div class="w-48 h-48 bg-white rounded-lg p-4 border border-gray-200 mx-auto mb-4 relative">
                                <!-- QR Code simulado -->
                                <div class="w-full h-full bg-gray-100 rounded flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400">
                                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                                    </svg>
                                    <span class="text-xs text-gray-500">SCANEIE O QR CODE</span>
                                </div>
                            </div>

                            <div class="mt-6">
                                <p class="text-sm font-semibold text-gray-700">Chave Pix (Copia e Cola)</p>
                                <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 font-mono text-sm text-gray-700 mb-4 break-all">
                                    00020126580014br.gov.bcb.pix0136{{ str_pad($payment->id, 32, '0', STR_PAD_RIGHT) }}@freelajob.com.br
                                </div>
                                <button class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all shadow-sm flex items-center justify-center gap-2">
                                    <i data-lucide="copy" class="w-4 h-4"></i>
                                    Copiar Chave Pix
                                </button>
                            </div>

                            <div class="mt-4">
                                <p class="text-xs text-gray-500">Expira em 30 minutos</p>
                                <p class="text-xs text-gray-500">Após o pagamento, aguarde a confirmação automática.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Instruções -->
                    <div class="lg:w-1/2">
                        <div class="bg-blue-50 rounded-2xl p-6 border border-blue-100">
                            <h4 class="text-lg font-bold text-blue-900 mb-4">Como pagar via Pix</h4>
                            <ol class="space-y-4 text-sm text-gray-700">
                                <li class="flex items-start gap-3">
                                    <span class="text-blue-600 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                                    <span>Abra seu aplicativo bancário ou carteira digital</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="text-blue-600 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                                    <span>Escolha a opção "Pagar com Pix" ou "Copia e Cola"</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="text-blue-600 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                                    <span>Cole a chave Pix acima ou escaneie o QR Code</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="text-blue-600 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold">4</span>
                                    <span>Confirme o pagamento e aguarde a liberação</span>
                                </li>
                            </ol>

                            <div class="mt-6 pt-6 border-t border-blue-200">
                                <p class="text-xs text-blue-600 font-medium">
                                    <i data-lucide="info" class="w-3 h-3 mr-1"></i>
                                    O valor ficará retido em custódia até o freelancer entregar o projeto e você liberar o pagamento.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @elseif ($method === 'cartao')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Pagamento com Cartão de Crédito</h3>

                <form action="{{ route('payments.process.post', $project) }}" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="method" value="cartao">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="card_name" class="block text-sm font-medium text-gray-700 mb-1">Nome no Cartão *</label>
                            <input type="text" name="card_name" id="card_name" required
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm"
                                   placeholder="João Silva">
                        </div>

                        <div>
                            <label for="card_number" class="block text-sm font-medium text-gray-700 mb-1">Número do Cartão *</label>
                            <input type="text" name="card_number" id="card_number" required
                                   maxlength="19"
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm"
                                   placeholder="1234 5678 9012 3456">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="card_expiry" class="block text-sm font-medium text-gray-700 mb-1">Validade *</label>
                            <input type="text" name="card_expiry" id="card_expiry" required
                                   maxlength="5"
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm"
                                   placeholder="12/25">
                        </div>

                        <div>
                            <label for="card_cvv" class="block text-sm font-medium text-gray-700 mb-1">CVV *</label>
                            <input type="text" name="card_cvv" id="card_cvv" required
                                   maxlength="4"
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm"
                                   placeholder="123">
                        </div>

                        <div>
                            <label for="installments" class="block text-sm font-medium text-gray-700 mb-1">Parcelas</label>
                            <select name="installments" id="installments"
                                    class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                                <option value="1">1x sem juros</option>
                                <option value="2">2x sem juros</option>
                                <option value="3">3x com juros</option>
                                <option value="6">6x com juros</option>
                                <option value="12">12x com juros</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <button type="submit"
                                class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-all shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                            <i data-lucide="credit-card" class="w-5 h-5"></i>
                            Pagar com Cartão
                        </button>
                    </div>

                    <div class="mt-4 text-center">
                        <p class="text-xs text-gray-500">
                            Taxa de cartão: 4,99% + R$ 0,50 será adicionada ao valor final.
                        </p>
                    </div>
                </form>
            </div>
        @elseif ($method === 'boleto')
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6">Pagamento com Boleto Bancário</h3>

                <div class="flex flex-col lg:flex-row gap-8">
                    <div class="lg:w-1/2">
                        <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 text-center">
                            <div class="w-16 h-16 bg-green-600 rounded-lg flex items-center justify-center mx-auto mb-4">
                                <i data-lucide="file-text" class="w-8 h-8 text-white"></i>
                            </div>

                            <h4 class="text-lg font-bold text-gray-900 mb-2">Boleto Bancário</h4>
                            <p class="text-gray-600 text-sm mb-6">O boleto poderá ser pago em qualquer lotérica, banco ou internet banking.</p>

                            <div class="bg-white p-4 rounded-lg border border-gray-200 mb-4">
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Código de Barras</p>
                                <div class="font-mono text-sm text-gray-700 break-all">
                                    {{ $result['data']['barcode'] ?? '00000000000000000000' }}
                                </div>
                            </div>

                            <a href="{{ route('payments.boleto', $payment) }}"
                               class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all shadow-sm flex items-center justify-center gap-2">
                                <i data-lucide="download" class="w-4 h-4"></i>
                                Baixar Boleto (PDF)
                            </a>
                        </div>
                    </div>

                    <div class="lg:w-1/2">
                        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100">
                            <h4 class="text-lg font-bold text-amber-900 mb-4">Informações Importantes</h4>
                            <ul class="space-y-3 text-sm text-gray-700">
                                <li class="flex items-start gap-3">
                                    <i data-lucide="clock" class="w-4 h-4 text-amber-600 mt-0.5"></i>
                                    <span>Vence em {{ $result['data']['expires_at'] ?? now()->addDays(3)->format('d/m/Y') }}</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i data-lucide="info" class="w-4 h-4 text-amber-600 mt-0.5"></i>
                                    <span>Após o pagamento, a confirmação pode levar até 2 dias úteis.</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i data-lucide="shield-alert" class="w-4 h-4 text-amber-600 mt-0.5"></i>
                                    <span>O valor ficará retido em custódia até a conclusão do projeto.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Ações rápidas -->
        <div class="flex gap-3">
            <a href="{{ route('payments.process', ['project' => $project, 'method' => 'pix']) }}"
               class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors flex items-center justify-center gap-2">
                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                Trocar para Pix
            </a>
            <a href="{{ route('payments.process', ['project' => $project, 'method' => 'cartao']) }}"
               class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors flex items-center justify-center gap-2">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
                Cartão de Crédito
            </a>
            <a href="{{ route('payments.process', ['project' => $project, 'method' => 'boleto']) }}"
               class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-colors flex items-center justify-center gap-2">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                Boleto Bancário
            </a>
        </div>
    </div>

</x-layout>
