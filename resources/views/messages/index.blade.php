<x-layout title="Chat do Projeto - FreelaJob">
<div class="max-w-3xl mx-auto py-8" x-data="{ text: '' }">
    <a href="{{ route('projects.show', $project) }}" class="inline-flex items-center gap-1 text-sm text-green-600 hover:text-green-700 font-medium">
        <i data-lucide="arrow-left" class="w-4 h-4"></i> Voltar ao projeto
    </a>

    <!-- Cabeçalho com a contraparte -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-4 flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-4 min-w-0">
            @if ($other)
                <img src="{{ $other->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($other->name).'&background=random&size=112' }}"
                     class="w-14 h-14 rounded-full object-cover shrink-0" alt="Foto de {{ $other->name }}">
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Conversando com</p>
                    <p class="font-bold text-gray-900">{{ $other->name }}</p>
                    <p class="text-sm text-green-700">
                        {{ $other->type === 'freelancer' ? 'Freelancer' : 'Cliente' }}
                        @if ($other->profile?->professional_title)
                            · {{ $other->profile->professional_title }}
                        @endif
                    </p>
                </div>
            @else
                <div>
                    <p class="font-bold text-gray-900">{{ $project->title }}</p>
                    <p class="text-sm text-gray-500">Aguardando freelancer contratado...</p>
                </div>
            @endif
        </div>
        <div class="text-right shrink-0">
            <p class="text-sm font-bold text-gray-900 truncate max-w-[220px]">{{ $project->title }}</p>
            <p class="text-xs text-gray-500">{{ $project->status }}</p>
            @if (($unreadCount ?? 0) > 0)
                <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                    <i data-lucide="mail" class="w-3 h-3"></i> {{ $unreadCount }} não lida(s) antes desta visita
                </span>
            @endif
        </div>
    </div>

    <div class="flex items-start gap-2 mt-4 text-xs text-gray-500">
        <i data-lucide="shield-alert" class="w-4 h-4 shrink-0 mt-0.5"></i>
        <p>Contato externo (e-mail, telefone ou links) é bloqueado por segurança. Use apenas as ferramentas da plataforma.</p>
    </div>

    <!-- Mensagens -->
    <div id="chat-messages" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mt-4 space-y-4">
        @forelse ($messages as $msg)
            <div class="flex gap-3 {{ $msg->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                <img src="{{ $msg->user->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($msg->user->name).'&background=random&size=72' }}"
                     class="w-9 h-9 rounded-full object-cover shrink-0" alt="">
                <div class="max-w-[80%]">
                    <div class="p-3.5 rounded-2xl {{ $msg->user_id === auth()->id() ? 'bg-green-600 text-white rounded-tr-sm' : 'bg-gray-100 text-gray-800 rounded-tl-sm' }}">
                        <p class="text-xs opacity-70 font-semibold">
                            {{ $msg->user->name }} · {{ $msg->created_at->format('d/m H:i') }}
                        </p>
                        <p class="text-sm whitespace-pre-wrap mt-0.5">{{ $msg->message }}</p>
                    </div>
                    @if ($msg->user_id === auth()->id())
                        <p class="text-right text-[11px] mt-1 flex items-center justify-end gap-1 {{ $msg->is_read ? 'text-green-600' : 'text-gray-400' }}">
                            <i data-lucide="{{ $msg->is_read ? 'check-check' : 'check' }}" class="w-3.5 h-3.5"></i>
                            {{ $msg->is_read ? 'Lida' : 'Enviada' }}
                        </p>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-10">
                <i data-lucide="message-circle" class="w-10 h-10 text-gray-300 mx-auto mb-3"></i>
                <p class="font-medium text-gray-700">Nenhuma mensagem ainda</p>
                <p class="text-sm text-gray-500 mt-1">
                    {{ $other ? 'Apresente-se e combine os próximos passos com '.$other->name.'.' : 'As mensagens aparecerão aqui quando o freelancer for contratado.' }}
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-4">{{ $messages->links() }}</div>

    <form method="POST" action="{{ route('projects.messages.store', $project) }}" class="mt-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        @csrf
        <label for="message" class="sr-only">Escreva sua mensagem</label>
        <textarea id="message" name="message" required maxlength="5000" rows="3" x-model="text"
                  class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm"
                  placeholder="Escreva sua mensagem...">{{ old('message') }}</textarea>
        <div class="flex flex-wrap items-center justify-between gap-2 mt-3">
            <p class="text-xs text-gray-400"><span x-text="text.length"></span>/5000 · Evite e-mails, telefones e links.</p>
            <button class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-sm">
                <i data-lucide="send" class="w-4 h-4"></i> Enviar mensagem
            </button>
        </div>
    </form>
</div>

<script>
    // Leva o usuário para a mensagem mais recente ao abrir o chat.
    document.addEventListener('DOMContentLoaded', function () {
        var box = document.getElementById('chat-messages');
        if (box) {
            box.scrollIntoView({ behavior: 'auto', block: 'end' });
        }
    });
</script>
</x-layout>
