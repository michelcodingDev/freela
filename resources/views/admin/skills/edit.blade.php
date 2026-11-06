<x-layout title="Editar Habilidade - FreelaJob">
<div class="max-w-2xl mx-auto py-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-900">Editar habilidade</h1>
        <a href="{{ route('admin.skills.index') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">← Voltar</a>
    </div>
    <form method="POST" action="{{ route('admin.skills.update', $skill) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6 space-y-4">
        @csrf
        @method('PUT')
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nome *</label>
            <input id="name" type="text" name="name" required maxlength="100" value="{{ old('name', $skill->name) }}"
                   class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none">
            @error('name')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        <div>
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1.5">Categoria *</label>
            <select id="category_id" name="category_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none">
                <option value="">Selecione a categoria</option>
                @foreach ($categories as $c)
                    <option value="{{ $c->id }}" {{ old('category_id', $skill->category_id) == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            @error('category_id')
                <p class="text-xs text-red-600 mt-1.5">{{ $message }}</p>
            @enderror
        </div>
        <button class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold">Salvar alterações</button>
    </form>
</div>
</x-layout>
