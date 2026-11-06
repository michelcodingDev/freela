<x-layout title="Chat do Projeto - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <a href="{{ route('projects.show', $project) }}" class="text-sm text-green-600 hover:text-green-700 font-medium">← Voltar ao projeto</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Chat — {{ $project->title }}</h1>
    <p class="text-gray-500 text-sm">Contato externo é bloqueado por segurança. Use apenas as ferramentas da plataforma.</p>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-4">
        @forelse ($messages as $msg)
            <div class="flex gap-3 {{ $msg->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                <img src="{{ $msg->user->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($msg->user->name).'&background=random' }}" class="w-9 h-9 rounded-full object-cover" alt="">
                <div class="max-w-[80%] p-3 rounded-2xl {{ $msg->user_id === auth()->id() ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-800' }}">
                    <p class="text-xs opacity-70 font-semibold">{{ $msg->user->name }} • {{ $msg->created_at->format('d/m H:i') }}</p>
                    <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-400 py-8">Nenhuma mensagem ainda. Comece a conversa!</p>
        @endforelse
    </div>

    <form method="POST" action="{{ route('projects.messages.store', $project) }}" class="mt-4 bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        @csrf
        <textarea name="message" required maxlength="5000" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm" placeholder="Escreva sua mensagem..."></textarea>
        <button class="mt-3 w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold">Enviar mensagem</button>
    </form>
</div>
</x-layout>
