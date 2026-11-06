<x-layout title="Nova Habilidade - FreelaJob">
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold text-gray-900">Nova habilidade</h1>
    <form method="POST" action="{{ route('admin.skills.store') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-6 space-y-4">
        @csrf
        <input type="text" name="name" required maxlength="100" placeholder="Ex: Laravel" class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
        <select name="category_id" required class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-xl text-sm">
            <option value="">Selecione a categoria</option>
            @foreach ($categories as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
            @endforeach
        </select>
        <button class="w-full py-3 bg-green-600 text-white rounded-xl font-semibold">Criar</button>
    </form>
</div>
</x-layout>
