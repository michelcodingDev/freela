<x-layout title="{{ $freelancer->name }} - FreelaJob">
<div class="max-w-5xl mx-auto py-8">
    <a href="{{ route('freelancers.index') }}" class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar para freelancers
    </a>

    @php
        $profile = $freelancer->profile;
        $avatar = $profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($freelancer->name).'&background=random&size=160';
        $location = collect([$profile?->city, $profile?->state])->filter()->join(' / ');
        $memberSince = $freelancer->created_at?->format('m/Y');
        $completed = (int) ($profile?->projects_completed ?? 0);
        $fullStars = (int) round($ratingAvg);
    @endphp

    <!-- Cabeçalho do perfil -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 sm:p-8 mt-6">
        <div class="flex flex-col sm:flex-row sm:items-start gap-5">
            <img src="{{ $avatar }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover shrink-0" alt="Foto de {{ $freelancer->name }}">
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $freelancer->name }}</h1>
                    @if ($profile?->identity_verified)
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                            <i data-lucide="badge-check" class="w-3.5 h-3.5"></i> Identidade verificada
                        </span>
                    @endif
                </div>
                <p class="text-green-700 font-medium mt-0.5">{{ $profile?->professional_title ?? 'Freelancer' }}</p>

                <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 text-sm text-gray-500 mt-2">
                    @if ($fullStars > 0)
                        <span class="inline-flex items-center gap-1 font-semibold text-gray-800">
                            <span class="text-amber-500" aria-hidden="true">{{ str_repeat('★', $fullStars) }}</span>
                            <span>{{ number_format($ratingAvg, 1, ',', '.') }}</span>
                            <span class="font-normal text-gray-500">({{ $ratingCount }} {{ $ratingCount === 1 ? 'avaliação' : 'avaliações' }})</span>
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="star" class="w-4 h-4 text-gray-300"></i> Sem avaliações ainda
                        </span>
                    @endif
                    <span class="inline-flex items-center gap-1">
                        <i data-lucide="briefcase" class="w-4 h-4"></i> {{ $completed }} {{ $completed === 1 ? 'projeto concluído' : 'projetos concluídos' }}
                    </span>
                    @if ($location)
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="map-pin" class="w-4 h-4"></i> {{ $location }}
                        </span>
                    @endif
                    @if ($memberSince)
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="calendar" class="w-4 h-4"></i> Na plataforma desde {{ $memberSince }}
                        </span>
                    @endif
                </div>

                @if ($freelancer->skills->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mt-4">
                        @foreach ($freelancer->skills as $skill)
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $skill->name }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="shrink-0 flex sm:flex-col gap-2">
                @auth
                    @if (auth()->id() !== $freelancer->id && auth()->user()->isCliente())
                        <a href="{{ route('projects.create') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">
                            <i data-lucide="plus" class="w-4 h-4"></i> Publicar projeto
                        </a>
                    @elseif (auth()->id() === $freelancer->id)
                        <a href="{{ route('profile.show') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm">
                            <i data-lucide="pencil" class="w-4 h-4"></i> Editar meu perfil
                        </a>
                    @else
                        <a href="{{ route('projects.index') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">
                            <i data-lucide="search" class="w-4 h-4"></i> Ver projetos abertos
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">
                        <i data-lucide="user-plus" class="w-4 h-4"></i> Contratar freelancer
                    </a>
                @endauth
            </div>
        </div>

        @if ($profile?->bio)
            <div class="border-t border-gray-100 mt-6 pt-6">
                <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-2">Sobre</h2>
                <p class="text-gray-700 text-sm leading-relaxed">{{ $profile->bio }}</p>
            </div>
        @endif
    </div>

    <!-- Portfólio -->
    @if ($freelancer->portfolioItems->isNotEmpty())
        <h2 class="text-lg font-bold text-gray-900 mt-10 mb-4">Portfólio</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($freelancer->portfolioItems as $item)
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    @if ($item->file_path)
                        <img src="{{ asset('storage/'.$item->file_path) }}" class="w-full h-44 object-cover" alt="{{ $item->title }}">
                    @else
                        <div class="w-full h-44 bg-gray-100 flex items-center justify-center text-gray-300">
                            <i data-lucide="image" class="w-10 h-10"></i>
                        </div>
                    @endif
                    <div class="p-5">
                        <p class="font-bold text-gray-900 text-sm">{{ $item->title }}</p>
                        @if ($item->description)
                            <p class="text-xs text-gray-600 mt-1.5 leading-relaxed">{{ $item->description }}</p>
                        @endif
                        @if ($item->external_url)
                            <a href="{{ $item->external_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs font-semibold text-green-600 hover:text-green-700 mt-3">
                                Ver projeto <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            </a>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    <!-- Avaliações -->
    <h2 class="text-lg font-bold text-gray-900 mt-10 mb-4">Avaliações</h2>

    <div class="grid lg:grid-cols-3 gap-6 items-start">
        <!-- Resumo -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:sticky lg:top-24">
            <div class="text-center">
                <p class="text-5xl font-bold text-gray-900">{{ number_format($ratingAvg, 1, ',', '.') }}</p>
                <p class="text-amber-500 text-xl mt-1" aria-hidden="true">
                    {{ str_repeat('★', $fullStars) }}{{ str_repeat('☆', max(0, 5 - $fullStars)) }}
                </p>
                <p class="text-sm text-gray-500 mt-1">{{ $ratingCount }} {{ $ratingCount === 1 ? 'avaliação' : 'avaliações' }}</p>
            </div>

            <div class="mt-6 space-y-2">
                @foreach ($distribution as $stars => $row)
                    <div class="flex items-center gap-2 text-xs">
                        <span class="w-3 font-medium text-gray-600">{{ $stars }}</span>
                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400"></i>
                        <div class="flex-1 h-2 rounded-full bg-gray-100 overflow-hidden">
                            <div class="h-full bg-amber-400 rounded-full" style="width: {{ $row['percent'] }}%"></div>
                        </div>
                        <span class="w-8 text-right text-gray-500">{{ $row['count'] }}</span>
                    </div>
                @endforeach
            </div>

            <p class="text-xs text-gray-400 mt-5 leading-relaxed">
                As avaliações são feitas por clientes após a conclusão de um projeto e só aparecem quando são públicas.
            </p>
        </div>

        <!-- Lista -->
        <div class="lg:col-span-2 space-y-3">
            @forelse ($reviews as $review)
                <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-3">
                            <img src="{{ $review->fromUser->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($review->fromUser->name).'&background=random&size=64' }}"
                                 class="w-10 h-10 rounded-full object-cover" alt="">
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $review->fromUser->name }}</p>
                                <p class="text-xs text-gray-400">{{ $review->created_at->format('d/m/Y') }}</p>
                            </div>
                        </div>
                        <p class="text-amber-500" aria-label="{{ $review->rating }} de 5 estrelas">
                            {{ str_repeat('★', (int) $review->rating) }}<span class="text-gray-200">{{ str_repeat('★', max(0, 5 - (int) $review->rating)) }}</span>
                        </p>
                    </div>

                    @if ($review->project)
                        <a href="{{ route('projects.show', $review->project) }}" class="inline-flex items-center gap-1 text-xs font-medium text-green-600 hover:text-green-700 mt-3">
                            <i data-lucide="folder" class="w-3.5 h-3.5"></i> {{ $review->project->title }}
                        </a>
                    @endif

                    @if ($review->comment)
                        <p class="text-sm text-gray-700 mt-2 leading-relaxed">{{ $review->comment }}</p>
                    @endif
                </article>
            @empty
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-10 text-center">
                    <i data-lucide="star" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                    <p class="font-medium text-gray-900">Nenhuma avaliação pública ainda</p>
                    <p class="text-sm text-gray-500 mt-1">As avaliações de clientes aparecerão aqui após projetos concluídos.</p>
                </div>
            @endforelse

            <div class="pt-2">{{ $reviews->links() }}</div>
        </div>
    </div>
</div>
</x-layout>
