<x-layout title="Portfólio - FreelaJob">
<div class="max-w-4xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Meu Portfólio</h1>

    <form method="POST" action="{{ route('portfolio.store') }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6 space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Título *</label>
            <input type="text" name="title" required maxlength="150" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
            <textarea name="description" maxlength="2000" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <input type="url" name="external_url" placeholder="URL externa" class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none">
            <input type="file" name="image" accept="image/*" class="px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        </div>
        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_public" value="1" checked> Público</label>
        <button class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold">Adicionar item</button>
    </form>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
        @forelse ($items as $item)
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
                @if ($item->file_path)
                    <img src="{{ asset('storage/'.$item->file_path) }}" class="w-full h-40 object-cover rounded-xl mb-3" alt="">
                @endif
                <p class="font-bold text-gray-900 text-sm">{{ $item->title }}</p>
                <p class="text-xs text-gray-600 mt-1">{{ $item->description }}</p>
                @if ($item->external_url)
                    <a href="{{ $item->external_url }}" target="_blank" class="text-xs text-green-600">Ver site →</a>
                @endif
                <form method="POST" action="{{ route('portfolio.destroy', $item) }}" class="mt-3" onsubmit="return confirm('Remover item?')">
                    @csrf @method('DELETE')
                    <button class="text-xs text-red-600 font-medium">Remover</button>
                </form>
            </div>
        @empty
            <p class="text-sm text-gray-400">Nenhum item ainda.</p>
        @endforelse
    </div>
</div>
</x-layout>
