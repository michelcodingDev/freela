<x-layout title="Ranking - FreelaJob">
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Ranking de freelancers</h1>
    <p class="text-gray-600 mb-8">Os profissionais mais bem avaliados da plataforma, com base em avaliações públicas e projetos concluídos.</p>

    @if ($freelancers->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
            <i data-lucide="trophy" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
            <p class="font-medium text-gray-900">Nenhum freelancer ranqueado ainda</p>
            <p class="text-sm text-gray-500 mt-1">O ranking aparece assim que houver avaliações públicas.</p>
            <a href="{{ route('freelancers.index') }}" class="inline-flex items-center gap-2 mt-5 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">
                Ver freelancers
            </a>
        </div>
    @else
        <!-- Pódio -->
        <div class="grid sm:grid-cols-3 gap-4 mb-8">
            @foreach ($podium as $index => $f)
                @php
                    $medal = match ($index) {
                        0 => 'bg-amber-100 text-amber-700',
                        1 => 'bg-gray-100 text-gray-700',
                        default => 'bg-orange-100 text-orange-700',
                    };
                @endphp
                <a href="{{ route('freelancers.show', $f) }}"
                   class="bg-white rounded-2xl border shadow-sm p-6 text-center hover:shadow-md transition {{ $index === 0 ? 'border-amber-200 sm:-translate-y-1' : 'border-gray-100' }}">
                    <div class="relative inline-block">
                        <img src="{{ $f->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($f->name).'&background=random&size=160' }}"
                             class="w-20 h-20 rounded-full object-cover mx-auto" alt="Foto de {{ $f->name }}">
                        <span class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full {{ $medal }} flex items-center justify-center font-bold text-sm border-2 border-white">
                            {{ $index + 1 }}
                        </span>
                    </div>
                    <p class="font-bold text-gray-900 mt-3 flex items-center justify-center gap-1.5">
                        {{ $f->name }}
                        @if ($f->profile?->identity_verified)
                            <i data-lucide="badge-check" class="w-4 h-4 text-blue-500"></i>
                        @endif
                    </p>
                    <p class="text-sm text-green-700 truncate">{{ $f->profile?->professional_title ?? 'Freelancer' }}</p>
                    <p class="text-sm mt-2 flex items-center justify-center gap-1 font-semibold">
                        <i data-lucide="star" class="w-4 h-4 text-amber-400"></i>
                        {{ number_format((float) ($f->rating_avg ?? 0), 1, ',', '.') }}
                        <span class="text-gray-500 font-normal">({{ $f->reviews_received_count ?? 0 }})</span>
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $f->completed_projects ?? 0 }} {{ ($f->completed_projects ?? 0) === 1 ? 'projeto concluído' : 'projetos concluídos' }}
                    </p>
                    <span class="inline-flex items-center gap-1 mt-3 px-3 py-1 rounded-full text-xs font-semibold {{ $medal }}">
                        <i data-lucide="trophy" class="w-3.5 h-3.5"></i> {{ $index + 1 }}º lugar
                    </span>
                </a>
            @endforeach
        </div>

        @if ($rest->isNotEmpty())
            <h2 class="text-lg font-bold text-gray-900 mb-4">Demais posições</h2>
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-100 overflow-hidden">
                @foreach ($rest as $f)
                    @php $position = $freelancers->search(fn ($u) => $u->id === $f->id) + 1; @endphp
                    <a href="{{ route('freelancers.show', $f) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50 transition">
                        <span class="w-8 text-center font-bold text-gray-400 shrink-0">#{{ $position }}</span>
                        <img src="{{ $f->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($f->name).'&background=random&size=96' }}"
                             class="w-12 h-12 rounded-full object-cover shrink-0" alt="">
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm text-gray-900 flex items-center gap-1.5">
                                <span class="truncate">{{ $f->name }}</span>
                                @if ($f->profile?->identity_verified)
                                    <i data-lucide="badge-check" class="w-4 h-4 text-blue-500 shrink-0"></i>
                                @endif
                            </p>
                            <p class="text-xs text-gray-500 truncate">{{ $f->profile?->professional_title ?? 'Freelancer' }}</p>
                        </div>
                        <div class="text-right shrink-0">
                            <p class="font-bold text-sm flex items-center justify-end gap-1">
                                <i data-lucide="star" class="w-4 h-4 text-amber-400"></i>
                                {{ number_format((float) ($f->rating_avg ?? 0), 1, ',', '.') }}
                                <span class="text-gray-400 font-normal">({{ $f->reviews_received_count ?? 0 }})</span>
                            </p>
                            <p class="text-xs text-gray-500">{{ $f->completed_projects ?? 0 }} concluídos</p>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-5 mt-8 text-sm text-blue-800 flex items-start gap-2">
            <i data-lucide="info" class="w-5 h-5 shrink-0 mt-0.5"></i>
            <p>
                <strong>Como funciona:</strong> o ranking considera apenas avaliações públicas (1 a 5 estrelas).
                Em caso de empate na média, desempata o número de avaliações e depois os projetos concluídos.
                Apenas freelancers ativos aparecem na lista.
            </p>
        </div>
    @endif
</div>
</x-layout>
