<x-layout title="Disputas - FreelaJob">
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Disputas</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-3">
        @forelse ($disputes as $d)
            <a href="{{ route('disputes.show', $d) }}" class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 hover:bg-gray-100">
                <div>
                    <p class="font-semibold text-gray-900 text-sm">{{ $d->project->title }}</p>
                    <p class="text-xs text-gray-500">{{ $d->created_at->format('d/m/Y') }} • {{ $d->status }}</p>
                </div>
                @if ($d->winner)
                    <span class="text-xs font-medium text-green-700 bg-green-100 px-2 py-1 rounded-full capitalize">Vencedor: {{ $d->winner }}</span>
                @endif
            </a>
        @empty
            <p class="text-sm text-gray-400">Nenhuma disputa.</p>
        @endforelse
        <div>{{ $disputes->links() }}</div>
    </div>
</div>
</x-layout>
