<x-layout title="Notificações - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-gray-900">Notificações</h1>
            @if ($unreadCount > 0)
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-500 text-white">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }} não lida(s)
                </span>
            @endif
        </div>
        @if ($unreadCount > 0)
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="inline-flex items-center gap-1.5 text-sm text-green-600 hover:text-green-700 font-medium">
                    <i data-lucide="check-check" class="w-4 h-4"></i> Marcar todas como lidas
                </button>
            </form>
        @endif
    </div>

    <!-- Filtros -->
    <div class="flex gap-2 mt-6" role="tablist" aria-label="Filtrar notificações">
        @foreach ([
            'all' => 'Todas',
            'unread' => 'Não lidas',
            'read' => 'Lidas',
        ] as $value => $label)
            <a href="{{ route('notifications.index', ['status' => $value]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ $filter === $value ? 'bg-green-600 text-white' : 'bg-white border border-gray-200 text-gray-600 hover:border-green-300' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse ($notifications as $n)
            <div class="bg-white rounded-2xl border shadow-sm overflow-hidden {{ $n->is_read ? 'border-gray-100' : 'border-green-200' }}">
                <form method="POST" action="{{ route('notifications.read', $n) }}" class="p-4 sm:p-5">
                    @csrf
                    <div class="flex items-start gap-4">
                        <div class="w-11 h-11 shrink-0 rounded-xl flex items-center justify-center {{ $n->iconClasses() }}">
                            <i data-lucide="{{ $n->icon() }}" class="w-5 h-5"></i>
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="font-semibold text-sm text-gray-900">{{ $n->title }}</p>
                                @if (! $n->is_read)
                                    <span class="w-2 h-2 rounded-full bg-green-500 shrink-0" title="Não lida"></span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-0.5 leading-relaxed">{{ $n->message }}</p>
                            <p class="text-xs text-gray-400 mt-1.5 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3 h-3"></i> {{ $n->created_at->diffForHumans() }}
                                <span>({{ $n->created_at->format('d/m/Y H:i') }})</span>
                            </p>

                            <div class="flex flex-wrap gap-2 mt-3">
                                @if ($n->url())
                                    <button type="submit" name="action" value="open"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-xs font-semibold">
                                        Ver detalhes <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                                    </button>
                                @endif
                                @if (! $n->is_read)
                                    <button type="submit" name="action" value="stay"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium">
                                        <i data-lucide="check" class="w-3.5 h-3.5"></i> Marcar lida
                                    </button>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs text-gray-400">
                                        <i data-lucide="check-check" class="w-3.5 h-3.5"></i> Lida
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @empty
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-12 text-center">
                <i data-lucide="bell-off" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                <p class="font-medium text-gray-900">
                    {{ $filter === 'unread' ? 'Nenhuma notificação não lida' : ($filter === 'read' ? 'Nenhuma notificação lida' : 'Nenhuma notificação ainda') }}
                </p>
                <p class="text-sm text-gray-500 mt-1">
                    Novidades sobre propostas, mensagens, pagamentos e saques aparecerão aqui.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $notifications->links() }}</div>
</div>
</x-layout>
