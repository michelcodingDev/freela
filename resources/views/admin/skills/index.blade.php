<x-layout title="Habilidades Admin - FreelaJob">
<div class="max-w-5xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Habilidades</h1>
        <a href="{{ route('admin.skills.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold">Nova habilidade</a>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
        @foreach ($skills as $s)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl">
                <div>
                    <p class="font-bold text-sm">{{ $s->name }}</p>
                    <p class="text-xs text-gray-500">{{ $s->category->name ?? '' }}</p>
                </div>
                <form method="POST" action="{{ route('admin.skills.destroy', $s) }}" onsubmit="return confirm('Excluir?')">@csrf @method('DELETE')<button class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-semibold">Excluir</button></form>
            </div>
        @endforeach
        <div>{{ $skills->links() }}</div>
    </div>
</div>
</x-layout>
