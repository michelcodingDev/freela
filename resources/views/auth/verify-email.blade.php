<x-layout title="Confirmar e-mail - FreelaJob">
    <div class="max-w-xl mx-auto py-10">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 text-center">
            <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mb-6">
                <i data-lucide="mail-check" class="w-8 h-8"></i>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-3">Confirme seu e-mail</h1>
            <p class="text-gray-600 text-sm leading-relaxed">
                Enviamos um link de confirmação para <strong class="text-gray-900">{{ auth()->user()->email }}</strong>.
                Clique no link para confirmar seu e-mail e liberar todos os recursos da conta.
            </p>
            <p class="text-gray-500 text-xs mt-3">
                Enquanto isso, você pode continuar usando a plataforma normalmente — a confirmação não bloqueia o acesso.
            </p>

            <form method="POST" action="{{ route('verification.send') }}" class="mt-7">
                @csrf
                <button
                    class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition">
                    <i data-lucide="send" class="w-4 h-4"></i> Reenviar e-mail de confirmação
                </button>
            </form>

            <div class="flex flex-col sm:flex-row gap-3 mt-3">
                <a href="{{ route('dashboard') }}"
                   class="flex-1 inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm">
                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Continuar para o dashboard
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl font-semibold text-sm">
                        <i data-lucide="log-out" class="w-4 h-4"></i> Usar outra conta
                    </button>
                </form>
            </div>

            <p class="text-xs text-gray-400 mt-6">
                Não recebeu o e-mail? Verifique a pasta de spam ou reenvie usando o botão acima.
            </p>
        </div>
    </div>
</x-layout>
