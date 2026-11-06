<x-layout title="{{ $freelancer->name }} - FreelaJob">
<div class="max-w-4xl mx-auto py-8">
    <a href="{{ route('freelancers.index') }}" class="text-sm text-green-600 font-medium">← Voltar</a>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-8 mt-6">
        <div class="flex items-center gap-4">
            <img src="{{ $freelancer->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($freelancer->name).'&background=random' }}" class="w-20 h-20 rounded-full object-cover" alt="">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $freelancer->name }}</h1>
                <p class="text-green-600 font-medium">{{ $freelancer->profile?->professional_title ?? '' }}</p>
                <p class="text-sm text-gray-500">★ {{ number_format($freelancer->reviewsReceived()->avg('rating') ?? 0, 1) }} • {{ $freelancer->profile?->projects_completed ?? 0 }} projetos concluídos</p>
            </div>
        </div>
        @if ($freelancer->profile?->bio)
            <p class="mt-6 text-gray-700 text-sm">{{ $freelancer->profile->bio }}</p>
        @endif
    </div>

    @if ($freelancer->portfolioItems->isNotEmpty())
        <h2 class="text-lg font-bold text-gray-900 mt-8 mb-4">Portfólio</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach ($freelancer->portfolioItems as $item)
                <div class="bg-white rounded-2xl border border-gray-100 p-4">
                    @if ($item->file_path)
                        <img src="{{ asset('storage/'.$item->file_path) }}" class="w-full h-40 object-cover rounded-xl mb-3" alt="">
                    @endif
                    <p class="font-bold text-sm">{{ $item->title }}</p>
                    <p class="text-xs text-gray-600 mt-1">{{ $item->description }}</p>
                </div>
            @endforeach
        </div>
    @endif

    <h2 class="text-lg font-bold text-gray-900 mt-8 mb-4">Avaliações</h2>
    <div class="space-y-3">
        @forelse ($reviews as $r)
            <div class="bg-white rounded-2xl border border-gray-100 p-4">
                <p class="text-sm"><strong>{{ $r->fromUser->name }}</strong> • {{ str_repeat('★', $r->rating) }}</p>
                <p class="text-sm text-gray-600 mt-1">{{ $r->comment }}</p>
            </div>
        @empty
            <p class="text-sm text-gray-400">Sem avaliações ainda.</p>
        @endforelse
    </div>
</div>
</x-layout>
