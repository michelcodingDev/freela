<x-layout title="Muitas tentativas">
    <div class="max-w-xl mx-auto text-center py-16">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-amber-100 text-amber-600 flex items-center justify-center mb-6">
            <i data-lucide="timer" class="w-8 h-8"></i>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 mb-3">Muitas tentativas</h1>
        <p class="text-gray-600 mb-2">
            Você fez muitas requisições em pouco tempo. Por segurança, aguarde alguns instantes antes de tentar novamente.
        </p>
        @php
            $retryAfter = isset($exception) ? ($exception->getHeaders()['Retry-After'] ?? null) : null;
        @endphp
        @if ($retryAfter)
            <p class="text-sm text-gray-500 mb-8">
                Tempo estimado de espera: {{ $retryAfter }} segundos.
            </p>
        @else
            <p class="text-sm text-gray-500 mb-8">Tente novamente em cerca de um minuto.</p>
        @endif

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('home') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar
            </a>
            <a href="{{ route('home') }}"
               class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold">
                <i data-lucide="home" class="w-4 h-4"></i> Página inicial
            </a>
        </div>
    </div>
</x-layout>
