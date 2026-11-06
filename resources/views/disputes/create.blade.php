<x-layout title="Abrir Disputa - FreelaJob">
<div class="max-w-2xl mx-auto py-8">
    <a href="{{ route('projects.show', $project) }}" class="text-sm text-green-600 hover:text-green-700 font-medium">← Voltar ao projeto</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Abrir disputa</h1>
    <p class="text-gray-600 text-sm">O valor em custódia será bloqueado até a moderação.</p>
    <form method="POST" action="{{ route('projects.disputes.store', $project) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo *</label>
            <textarea name="reason" required minlength="20" maxlength="2000" rows="5" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm" placeholder="Descreva o motivo com detalhes..."></textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Seu argumento</label>
            <textarea name="argument" maxlength="2000" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 outline-none text-sm"></textarea>
        </div>
        <button class="w-full py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold">Abrir disputa</button>
    </form>
</div>
</x-layout>
