<x-layout title="Notificações - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Notificações</h1>
        <form method="POST" action="{{ route('notifications.readAll') }}">@csrf<button class="text-sm text-green-600 font-medium hover:text-green-700">Marcar todas como lidas</button></form>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-3">
        @forelse ($notifications as $n)
            <form method="POST" action="{{ route('notifications.read', $n) }}" class="flex items-start justify-between p-4 rounded-xl border {{ $n->is_read ? 'border-gray-100 bg-white' : 'border-green-200 bg-green-50' }}">
                @csrf
                <div>
                    <p class="font-semibold text-sm text-gray-900">{{ $n->title }}</p>
                    <p class="text-sm text-gray-600">{{ $n->message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $n->created_at->format('d/m/Y H:i') }}</p>
                </div>
                @if (!$n->is_read)
                    <button class="text-xs font-medium text-green-700 underline">Marcar lida</button>
                @endif
            </form>
        @empty
            <p class="text-sm text-gray-400">Nenhuma notificação.</p>
        @endforelse
        <div>{{ $notifications->links() }}</div>
    </div>
</div>
</x-layout>
