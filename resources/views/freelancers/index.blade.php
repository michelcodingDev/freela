<x-layout title="Freelancers - FreelaJob">
<div class="max-w-7xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Freelancers</h1>
    <p class="text-gray-600 mb-8">Encontre profissionais verificados para o seu projeto.</p>

    <form method="GET" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-8 flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium mb-1">Buscar</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nome, título ou bio..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <select name="order" class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm">
            <option value="">Mais recentes</option>
            <option value="melhor_avaliacao" {{ request('order') === 'melhor_avaliacao' ? 'selected' : '' }}>Melhor avaliação</option>
            <option value="mais_projetos" {{ request('order') === 'mais_projetos' ? 'selected' : '' }}>Mais projetos</option>
        </select>
        <button class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-medium">Filtrar</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($freelancers as $f)
            <article class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition">
                <div class="flex items-center gap-3 mb-4">
                    <img src="{{ $f->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($f->name).'&background=random&size=112' }}" class="w-14 h-14 rounded-full object-cover" alt="">
                    <div class="min-w-0">
                        <p class="font-bold text-gray-900 flex items-center gap-1.5">
                            <span class="truncate">{{ $f->name }}</span>
                            @if ($f->profile?->identity_verified)
                                <i data-lucide="badge-check" class="w-4 h-4 text-blue-500 shrink-0" title="Identidade verificada"></i>
                            @endif
                        </p>
                        <p class="text-sm text-green-600 truncate">{{ $f->profile?->professional_title ?? 'Freelancer' }}</p>
                        @if ($f->profile?->city)
                            <p class="text-xs text-gray-400 flex items-center gap-1 mt-0.5">
                                <i data-lucide="map-pin" class="w-3 h-3"></i>{{ $f->profile->city }}{{ $f->profile->state ? ' / '.$f->profile->state : '' }}
                            </p>
                        @endif
                    </div>
                </div>
                <p class="text-sm text-gray-600 line-clamp-2 mb-3 min-h-[2.5rem]">{{ $f->profile?->bio ?? 'Sem bio.' }}</p>
                @if ($f->skills->isNotEmpty())
                    <div class="flex flex-wrap gap-1.5 mb-3">
                        @foreach ($f->skills->take(3) as $skill)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-100 text-gray-600">{{ $skill->name }}</span>
                        @endforeach
                        @if ($f->skills->count() > 3)
                            <span class="px-2 py-0.5 rounded-full text-[11px] font-medium bg-gray-50 text-gray-400">+{{ $f->skills->count() - 3 }}</span>
                        @endif
                    </div>
                @endif
                <div class="flex items-center gap-4 text-xs text-gray-500 mb-4">
                    <span class="inline-flex items-center gap-1">
                        <i data-lucide="star" class="w-3.5 h-3.5 text-amber-400"></i>
                        {{ number_format($f->rating_avg ?? 0, 1, ',', '.') }} ({{ $f->reviews_received_count ?? 0 }})
                    </span>
                    <span class="inline-flex items-center gap-1">
                        <i data-lucide="briefcase" class="w-3.5 h-3.5"></i>
                        {{ $f->profile?->projects_completed ?? 0 }} projetos
                    </span>
                </div>
                <a href="{{ route('freelancers.show', $f) }}" class="block text-center py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl text-sm font-semibold">Ver perfil</a>
            </article>
        @empty
            <p class="text-sm text-gray-400">Nenhum freelancer encontrado.</p>
        @endforelse
    </div>
    <div class="mt-8 flex justify-center">{{ $freelancers->links() }}</div>
</div>
</x-layout>
