<x-layout title="Convites - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Convites recebidos</h1>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6 space-y-3">
        @forelse ($invites as $invite)
            <div class="flex flex-wrap items-center justify-between gap-3 p-4 bg-gray-50 rounded-xl">
                <div>
                    <p class="font-semibold text-sm">{{ $invite->project->title }}</p>
                    <p class="text-xs text-gray-500">De {{ $invite->client->name }} • {{ $invite->status }}</p>
                    @if ($invite->message)
                        <p class="text-xs text-gray-600 mt-1">{{ $invite->message }}</p>
                    @endif
                </div>
                @if ($invite->status === 'convidado')
                    <div class="flex gap-2">
                        <form method="POST" action="{{ route('invites.accept', $invite) }}">@csrf<button class="px-3 py-2 bg-green-600 text-white rounded-lg text-xs font-semibold">Aceitar</button></form>
                        <form method="POST" action="{{ route('invites.decline', $invite) }}">@csrf<button class="px-3 py-2 bg-gray-200 text-gray-700 rounded-lg text-xs font-semibold">Recusar</button></form>
                    </div>
                @elseif ($invite->status === 'aceito')
                    <a href="{{ route('projects.show', $invite->project) }}" class="px-3 py-2 bg-blue-600 text-white rounded-lg text-xs font-semibold">Ver projeto</a>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-400">Nenhum convite.</p>
        @endforelse
        <div>{{ $invites->links() }}</div>
    </div>
</div>
</x-layout>
