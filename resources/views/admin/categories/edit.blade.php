<x-layout title="Editar Categoria - FreelaJob">
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Editar categoria</h1>
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6 space-y-4">
        @csrf @method('PUT')
        <input type="text" name="name" required maxlength="255" value="{{ old('name', $category->name) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <input type="text" name="slug" required maxlength="255" value="{{ old('slug', $category->slug) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <input type="text" name="icon" maxlength="100" value="{{ old('icon', $category->icon) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <input type="text" name="color" maxlength="20" value="{{ old('color', $category->color) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <textarea name="description" rows="3" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">{{ old('description', $category->description) }}</textarea>
        <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <label class="flex gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}> Ativa</label>
        <button class="w-full py-3 bg-blue-600 text-white rounded-xl font-semibold">Salvar</button>
    </form>
</div>
</x-layout>
