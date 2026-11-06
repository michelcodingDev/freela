<x-layout title="Abrir Disputa - FreelaJob">
<div class="max-w-2xl mx-auto py-8">
    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar ao projeto
    </a>

    <h1 class="text-2xl font-bold text-gray-900 mt-3">Abrir disputa</h1>
    <p class="text-gray-600 text-sm">Projeto: <strong>{{ $project->title }}</strong></p>

    @if ($project->acceptedProposal?->freelancer)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-6 flex items-center gap-4">
            <img src="{{ $project->acceptedProposal->freelancer->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($project->acceptedProposal->freelancer->name).'&background=random&size=112' }}"
                 class="w-14 h-14 rounded-full object-cover" alt="">
            <div class="text-sm">
                <p class="text-gray-500">
                    @if (auth()->id() === $project->client_id)
                        Você está contestando o freelancer
                    @else
                        Você está contestando o cliente
                    @endif
                </p>
                <p class="font-bold text-gray-900">
                    {{ auth()->id() === $project->client_id ? $project->acceptedProposal->freelancer->name : $project->client->name }}
                </p>
                <p class="text-xs text-gray-500">{{ $project->client->name }} × {{ $project->acceptedProposal->freelancer->name }}</p>
            </div>
        </div>
    @endif

    <div class="mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 flex items-start gap-2">
        <i data-lucide="lock" class="w-5 h-5 shrink-0 mt-0.5"></i>
        <p>
            O valor em custódia será bloqueado até a moderação.
            @if ($escrowPayment)
                Valor bloqueado: <strong>R$ {{ number_format((float) $escrowPayment->amount, 2, ',', '.') }}</strong>
                (método: {{ $escrowPayment->method ?? '—' }}).
            @endif
        </p>
    </div>

    <form method="POST" action="{{ route('projects.disputes.store', $project) }}"
          class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-5"
          x-data="{ reason: @js(old('reason', '')), argument: @js(old('argument', '')) }">
        @csrf
        <div>
            <label for="reason" class="block text-sm font-medium text-gray-700 mb-1.5">Motivo *</label>
            <textarea id="reason" name="reason" required minlength="20" maxlength="2000" rows="5" x-model="reason"
                      class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm"
                      placeholder="Descreva o motivo com detalhes: o que foi combinado, o que foi entregue e o que faltou.">{{ old('reason') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mínimo de 20 caracteres. <span x-text="reason.length"></span>/2000</p>
            @error('reason')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="argument" class="block text-sm font-medium text-gray-700 mb-1.5">Seu argumento</label>
            <textarea id="argument" name="argument" maxlength="2000" rows="3" x-model="argument"
                      class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm"
                      placeholder="Explique sua posição (opcional)...">{{ old('argument') }}</textarea>
            <p class="text-xs text-gray-400 mt-1"><span x-text="argument.length"></span>/2000</p>
            @error('argument')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        <button class="w-full py-3.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold flex items-center justify-center gap-2">
            <i data-lucide="shield-alert" class="w-5 h-5"></i> Abrir disputa
        </button>
    </form>
</div>
</x-layout>
