<x-layout title="Nova Categoria - FreelaJob">
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Nova categoria</h1>
    <form method="POST" action="{{ route('admin.categories.store') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6 space-y-4">
        @csrf
        <input type="text" name="name" required maxlength="255" placeholder="Nome" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <input type="text" name="slug" required maxlength="255" placeholder="slug-unico" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <input type="text" name="icon" maxlength="100" placeholder="Ícone Lucide (opcional)" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <input type="text" name="color" maxlength="20" placeholder="#1e3a5f" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <textarea name="description" rows="3" placeholder="Descrição" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm"></textarea>
        <input type="number" name="sort_order" value="0" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <label class="flex gap-2 text-sm"><input type="checkbox" name="is_active" value="1" checked> Ativa</label>
        <button class="w-full py-3 bg-green-600 text-white rounded-xl font-semibold">Criar</button>
    </form>
</div>
</x-layout>
