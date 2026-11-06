<x-layout title="Disputa - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <a href="{{ route('disputes.index') }}" class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar para disputas
    </a>

    <div class="flex flex-wrap items-start justify-between gap-3 mt-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Disputa — {{ $dispute->project->title }}</h1>
            <p class="text-sm text-gray-500 mt-1">Aberta em {{ $dispute->created_at->format('d/m/Y H:i') }} ({{ $dispute->created_at->diffForHumans() }})</p>
        </div>
        @if ($dispute->status === 'aberta')
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-amber-100 text-amber-700">
                <i data-lucide="loader" class="w-4 h-4"></i> Em análise
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium bg-green-100 text-green-700">
                <i data-lucide="check-circle" class="w-4 h-4"></i> Resolvida
            </span>
        @endif
    </div>

    <!-- Partes -->
    <div class="grid sm:grid-cols-2 gap-4 mt-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Cliente</p>
            <div class="flex items-center gap-3 mt-2">
                <img src="{{ $dispute->client->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($dispute->client->name).'&background=random&size=80' }}"
                     class="w-11 h-11 rounded-full object-cover" alt="">
                <div>
                    <p class="font-bold text-gray-900 text-sm">{{ $dispute->client->name }}</p>
                    <p class="text-xs text-gray-500">{{ $dispute->client->profile?->professional_title ?? 'Cliente' }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Freelancer</p>
            <div class="flex items-center gap-3 mt-2">
                <img src="{{ $dispute->freelancer->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($dispute->freelancer->name).'&background=random&size=80' }}"
                     class="w-11 h-11 rounded-full object-cover" alt="">
                <div>
                    <p class="font-bold text-gray-900 text-sm">{{ $dispute->freelancer->name }}</p>
                    <p class="text-xs text-gray-500">{{ $dispute->freelancer->profile?->professional_title ?? 'Freelancer' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Escrow -->
    @if ($escrowPayment)
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mt-4 flex items-start gap-3">
            <i data-lucide="lock" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5"></i>
            <div class="text-sm">
                <p class="font-bold text-blue-900">Valor em custódia</p>
                <p class="text-blue-800 mt-1">
                    R$ {{ number_format((float) $escrowPayment->amount, 2, ',', '.') }}
                    · Líquido estimado: R$ {{ number_format((float) $escrowPayment->net_amount, 2, ',', '.') }}
                    · Método: {{ $escrowPayment->method ?? '—' }}
                    · Status: {{ $escrowPayment->status }}
                </p>
                <p class="text-blue-700 text-xs mt-1">O valor permanece bloqueado até a decisão da moderação.</p>
            </div>
        </div>
    @endif

    <!-- Motivo e argumentos -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-4 space-y-5">
        <div>
            <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Motivo da disputa</h2>
            <p class="text-gray-700 text-sm mt-2 leading-relaxed">{{ $dispute->reason }}</p>
        </div>

        <div class="grid sm:grid-cols-2 gap-4 border-t border-gray-100 pt-5">
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Argumento do cliente</h3>
                <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                    {{ $dispute->client_argument ?: 'Nenhum argumento adicional informado.' }}
                </p>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-gray-900">Argumento do freelancer</h3>
                <p class="text-sm text-gray-600 mt-2 leading-relaxed">
                    {{ $dispute->freelancer_argument ?: 'Nenhum argumento adicional informado.' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2 border-t border-gray-100 pt-5">
            <a href="{{ route('projects.show', $dispute->project) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">
                <i data-lucide="folder" class="w-4 h-4"></i> Ver projeto
            </a>
            @if ($dispute->project->isParticipant(auth()->id()))
                <a href="{{ route('projects.messages.index', $dispute->project) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg text-sm font-medium">
                    <i data-lucide="message-circle" class="w-4 h-4"></i> Chat do projeto
                </a>
            @endif
        </div>
    </div>

    <!-- Resolução -->
    @if ($dispute->status === 'resolvida')
        <div class="bg-green-50 border border-green-200 rounded-2xl p-5 mt-4">
            <p class="font-bold text-green-900 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-5 h-5"></i> Resolução
            </p>
            <p class="text-sm text-green-800 mt-2">
                Vencedor: <strong class="capitalize">{{ $dispute->winner }}</strong>
                @if ($dispute->winner === 'parcial' && $dispute->partial_percentage !== null)
                    · {{ number_format((float) $dispute->partial_percentage, 2, ',', '.') }}% ao freelancer
                @endif
            </p>
            @if ($dispute->resolution_notes)
                <p class="text-sm text-green-800 mt-2 leading-relaxed">{{ $dispute->resolution_notes }}</p>
            @endif
            <p class="text-xs text-green-700 mt-3">
                Resolvida em {{ $dispute->resolved_at?->format('d/m/Y H:i') }}
                @if ($dispute->resolver) · por {{ $dispute->resolver->name }} @endif
            </p>
        </div>
    @endif

    @auth
        @if (auth()->user()->isAdmin() && $dispute->status !== 'resolvida')
            <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}"
                  class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-4">
                @csrf
                <h2 class="font-bold text-gray-900">Resolver disputa</h2>
                <div>
                    <label for="winner" class="block text-sm font-medium text-gray-700 mb-1.5">Vencedor</label>
                    <select id="winner" name="winner" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
                        <option value="cliente">Cliente (estornar pagamento)</option>
                        <option value="freelancer">Freelancer (liberar pagamento)</option>
                        <option value="parcial">Parcial (dividir valor)</option>
                    </select>
                </div>
                <div>
                    <label for="partial_percentage" class="block text-sm font-medium text-gray-700 mb-1.5">% parcial ao freelancer (se aplicável)</label>
                    <input id="partial_percentage" type="number" name="partial_percentage" step="0.01" min="0" max="100"
                           class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm" placeholder="Ex.: 50">
                </div>
                <div>
                    <label for="resolution_notes" class="block text-sm font-medium text-gray-700 mb-1.5">Notas da resolução *</label>
                    <textarea id="resolution_notes" name="resolution_notes" required maxlength="2000" rows="3"
                              class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm" placeholder="Notas da resolução..."></textarea>
                </div>
                @error('winner')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
                @error('partial_percentage')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror
                <button class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold">Resolver disputa</button>
            </form>
        @endif
    @endauth
</div>
</x-layout>
