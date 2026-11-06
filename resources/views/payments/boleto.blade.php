<x-layout title="Boleto - FreelaJob">

    <div class="max-w-2xl mx-auto py-8">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Boleto Bancário</h1>
                    <p class="text-gray-600 text-sm">{{ $payment->project->title }}</p>
                </div>
                <a href="{{ route('payments.process', ['project' => $payment->project, 'method' => 'boleto']) }}"
                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                    ← Voltar
                </a>
            </div>

            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="file-text" class="w-10 h-10 text-white"></i>
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Boleto para Impressão</h2>
                <p class="text-gray-600 text-sm">Valor: <span class="font-bold">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span></p>
            </div>

            <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 mb-6">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Dados do Boleto</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-200">
                        <span class="text-gray-600 text-sm">Nosso Número</span>
                        <span class="font-mono text-sm text-gray-700">{{ str_pad($payment->id, 11, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-200">
                        <span class="text-gray-600 text-sm">Código de Barras</span>
                        <span class="font-mono text-sm text-gray-700 break-all">
                            00000.00000 {{ str_pad($payment->id, 10, '0', STR_PAD_LEFT) }} 00000.000001 9 0000000000{{ $payment->id }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-200">
                        <span class="text-gray-600 text-sm">Valor</span>
                        <span class="text-sm font-semibold text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-white rounded-lg border border-gray-200">
                        <span class="text-gray-600 text-sm">Vencimento</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ now()->addDays(3)->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-blue-50 rounded-xl p-6 border border-blue-100">
                <h3 class="text-sm font-semibold text-blue-800 uppercase tracking-wider mb-3">Como Pagar</h3>
                <ol class="space-y-3 text-sm text-gray-700">
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold">1</span>
                        <span>Imprima este boleto ou copie o código de barras</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold">2</span>
                        <span>Pague em qualquer lotérica, banco ou pelo internet banking</span>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="text-blue-600 w-5 h-5 rounded-full flex items-center justify-center text-xs font-bold">3</span>
                        <span>Aguarde a confirmação (até 2 dias úteis)</span>
                    </li>
                </ol>
            </div>

            <div class="mt-8 flex gap-3">
                <button onclick="window.print()"
                        class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Imprimir Boleto
                </button>
                <a href="{{ route('projects.show', $payment->project) }}"
                   class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-medium transition-all flex items-center justify-center gap-2">
                    <i data-lucide="x" class="w-4 h-4"></i>
                    Cancelar
                </a>
            </div>
        </div>
    </div>

</x-layout>
