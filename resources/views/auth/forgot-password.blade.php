<x-layout title="Recuperar senha - FreelaJob">
<div class="flex min-h-[calc(100vh-200px)] items-center justify-center px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900">Recuperar senha</h2>
        <p class="text-sm text-gray-600 mt-2">Enviaremos um link para redefinir a senha.</p>
        @if (session('status'))
            <div class="mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">{{ session('status') }}</div>
        @endif
        <form method="POST" action="{{ route('password.email') }}" class="mt-6 space-y-4">
            @csrf
            <input type="email" name="email" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm" placeholder="seu@email.com">
            @error('email')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
            <button class="w-full py-3 bg-green-600 text-white rounded-lg font-semibold">Enviar link</button>
        </form>
        <a href="{{ route('login') }}" class="block text-center text-sm text-green-600 mt-4">Voltar ao login</a>
    </div>
</div>
</x-layout>
