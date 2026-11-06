<x-layout title="Suporte">
    <div class="max-w-3xl mx-auto">
        <a href="{{ route('home') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">← Voltar ao início</a>

        <h1 class="text-3xl font-bold text-gray-900 mt-3 mb-2">Central de Suporte</h1>
        <p class="text-gray-600 mb-8">Encontre respostas rápidas ou entre em contato com nossa equipe.</p>

        <div class="grid sm:grid-cols-2 gap-4 mb-8">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center mb-3">
                    <i data-lucide="mail" class="w-5 h-5"></i>
                </div>
                <h2 class="font-bold text-gray-900 mb-1">E-mail</h2>
                <p class="text-sm text-gray-600">Resposta em até 1 dia útil.</p>
                <a href="mailto:suporte@freelajob.com" class="text-sm text-green-600 hover:underline font-medium">suporte@freelajob.com</a>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="w-10 h-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
                    <i data-lucide="shield-question" class="w-5 h-5"></i>
                </div>
                <h2 class="font-bold text-gray-900 mb-1">Disputas</h2>
                <p class="text-sm text-gray-600">Problemas com um projeto? Abra uma disputa.</p>
                <a href="{{ auth()->check() ? route('disputes.index') : route('login') }}" class="text-sm text-green-600 hover:underline font-medium">Acessar disputas</a>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Perguntas frequentes</h2>

            <div class="space-y-4" x-data="{ open: null }">
                @foreach ([
                    ['q' => 'Como funciona o pagamento em custódia?', 'a' => 'O cliente paga e o valor fica retido pela plataforma. O freelancer só recebe após a entrega ser aprovada ou após a liberação automática.'], 
                    ['q' => 'Quando posso solicitar saque?', 'a' => 'Assim que um pagamento é liberado, o valor aparece como saldo disponível na carteira. O saque é feito via Pix, respeitando o valor mínimo de R$ 10,00.'],
                    ['q' => 'Posso retirar uma proposta enviada?', 'a' => 'Sim, enquanto a proposta estiver pendente e o projeto ainda estiver recebendo propostas. Depois disso, não é possível.'],
                    ['q' => 'O que acontece se não houver acordo?', 'a' => 'Qualquer uma das partes pode abrir uma disputa. Nossa equipe analisa o caso e decide pela liberação do pagamento ou reembolso.'],
                ] as $index => $faq)
                    <div class="border border-gray-100 rounded-xl overflow-hidden">
                        <button type="button" @click="open = open === {{ $index }} ? null : {{ $index }}"
                                class="w-full flex items-center justify-between gap-3 px-4 py-3 text-left font-medium text-gray-900 hover:bg-gray-50">
                            <span>{{ $faq['q'] }}</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 shrink-0"></i>
                        </button>
                        <div x-show="open === {{ $index }}" x-cloak x-transition class="px-4 pb-4 text-sm text-gray-600">
                            {{ $faq['a'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <p class="text-sm text-gray-500 mt-6 text-center">
            Ao usar a plataforma você concorda com os
            <a href="{{ route('pages.terms') }}" class="text-green-600 hover:underline font-medium">Termos de Uso</a>
            e a
            <a href="{{ route('pages.privacy') }}" class="text-green-600 hover:underline font-medium">Política de Privacidade</a>.
        </p>
    </div>
</x-layout>
