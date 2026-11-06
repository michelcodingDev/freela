<x-layout title="Disputa - FreelaJob">
<div class="max-w-3xl mx-auto py-8">
    <a href="{{ route('disputes.index') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">← Voltar</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Disputa — {{ $dispute->project->title }}</h1>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-4">
        <p class="text-sm"><strong>Status:</strong> {{ $dispute->status }}</p>
        <p class="text-sm"><strong>Motivo:</strong> {{ $dispute->reason }}</p>
        <p class="text-sm"><strong>Cliente:</strong> {{ $dispute->client->name }}</p>
        <p class="text-sm"><strong>Freelancer:</strong> {{ $dispute->freelancer->name }}</p>
        @if ($dispute->resolution_notes)
            <p class="text-sm"><strong>Resolução:</strong> {{ $dispute->resolution_notes }} (vencedor: {{ $dispute->winner }})</p>
        @endif
    </div>
    @auth
        @if (auth()->user()->isAdmin() && $dispute->status !== 'resolvida')
            <form method="POST" action="{{ route('admin.disputes.resolve', $dispute) }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vencedor</label>
                    <select name="winner" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
                        <option value="cliente">Cliente</option>
                        <option value="freelancer">Freelancer</option>
                        <option value="parcial">Parcial</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">% parcial (se aplicável)</label>
                    <input type="number" name="partial_percentage" step="0.01" min="0" max="100" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
                </div>
                <textarea name="resolution_notes" required maxlength="2000" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm" placeholder="Notas da resolução..."></textarea>
                <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold">Resolver disputa</button>
            </form>
        @endif
    @endauth
</div>
</x-layout>
