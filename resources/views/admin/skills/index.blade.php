<x-layout title="Habilidades Admin - FreelaJob">
<div class="max-w-5xl mx-auto py-8">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Habilidades</h1>
            <p class="text-gray-600 text-sm">Organize as habilidades por categoria.</p>
        </div>
        <a href="{{ route('admin.skills.create') }}" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Nova habilidade</a>
    </div>

    <form method="GET" action="{{ route('admin.skills.index') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <div class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Buscar por nome ou categoria..."
                   class="flex-1 min-w-[220px] px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 outline-none text-sm">
            <select name="category_id" class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 outline-none">
                <option value="">Todas as categorias</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <button class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-semibold">Filtrar</button>
            @if (request()->hasAny(['search', 'category_id']))
                <a href="{{ route('admin.skills.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium">Limpar</a>
            @endif
        </div>
    </form>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm divide-y divide-gray-100 overflow-hidden">
        @forelse ($skills as $s)
            <div class="flex flex-wrap items-center justify-between gap-3 p-4">
                <div class="min-w-0">
                    <p class="font-bold text-sm text-gray-900">{{ $s->name }}</p>
                    <p class="text-xs text-gray-500">
                        {{ $s->category->name ?? 'Sem categoria' }}
                        · {{ $s->projects_count }} projeto(s)
                        · {{ $s->users_count }} freelancer(s)
                    </p>
                </div>
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('admin.skills.edit', $s) }}" class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-xs font-medium">Editar</a>
                    <form method="POST" action="{{ route('admin.skills.destroy', $s) }}"
                          onsubmit="return confirm('Excluir esta habilidade?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1.5 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-xs font-medium">Excluir</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="p-10 text-center text-sm text-gray-400">Nenhuma habilidade encontrada.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $skills->links() }}</div>
</div>
</x-layout>
