<x-layout title="Categorias Admin - FreelaJob">
<div class="max-w-5xl mx-auto py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Categorias</h1>
        <a href="{{ route('admin.categories.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold">Nova categoria</a>
    </div>
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-3">
        @forelse ($categories as $c)
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl">
                <div>
                    <p class="font-bold text-sm text-gray-900">{{ $c->name }} <span class="text-gray-400 font-normal">({{ $c->slug }})</span></p>
                    <p class="text-xs text-gray-500">{{ $c->is_active ? 'Ativa' : 'Inativa' }} • ordem {{ $c->sort_order }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('admin.categories.edit', $c) }}" class="px-3 py-2 bg-blue-100 text-blue-700 rounded-lg text-xs font-semibold">Editar</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $c) }}" onsubmit="return confirm('Excluir categoria?')">@csrf @method('DELETE')<button class="px-3 py-2 bg-red-100 text-red-700 rounded-lg text-xs font-semibold">Excluir</button></form>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400">Nenhuma categoria.</p>
        @endforelse
    </div>
</div>
</x-layout>
