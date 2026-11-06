<x-layout title="Ranking - FreelaJob">
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Ranking de freelancers</h1>
    <p class="text-gray-600 mb-6">Os mais bem avaliados da plataforma.</p>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-100">
        @foreach ($freelancers as $i => $f)
            <a href="{{ route('freelancers.show', $f) }}" class="flex items-center gap-4 p-4 hover:bg-gray-50">
                <span class="w-8 text-center font-bold text-gray-400">#{{ $i + 1 }}</span>
                <img src="{{ $f->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($f->name).'&background=random' }}" class="w-12 h-12 rounded-full object-cover" alt="">
                <div class="flex-1">
                    <p class="font-bold text-sm text-gray-900">{{ $f->name }}</p>
                    <p class="text-xs text-gray-500">{{ $f->profile?->professional_title ?? '' }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-sm">★ {{ number_format($f->rating_avg ?? 0, 1) }}</p>
                    <p class="text-xs text-gray-500">{{ $f->profile?->projects_completed ?? 0 }} projetos</p>
                </div>
            </a>
        @endforeach
        @if ($freelancers->isEmpty())
            <p class="p-6 text-sm text-gray-400">Sem freelancers ranqueados.</p>
        @endif
    </div>
</div>
</x-layout>
