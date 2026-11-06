<x-layout title="Redefinir senha - FreelaJob">
<div class="flex min-h-[calc(100vh-200px)] items-center justify-center px-4">
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
        <h2 class="text-2xl font-bold text-gray-900">Redefinir senha</h2>
        <form method="POST" action="{{ route('password.update') }}" class="mt-6 space-y-4">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="email" name="email" required value="{{ old('email', $email) }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm">
            <input type="password" name="password" required minlength="8" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm" placeholder="Nova senha">
            <input type="password" name="password_confirmation" required minlength="8" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm" placeholder="Confirmar senha">
            @error('email')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
            @error('password')<p class="text-red-500 text-xs">{{ $message }}</p>@enderror
            <button class="w-full py-3 bg-green-600 text-white rounded-lg font-semibold">Redefinir senha</button>
        </form>
    </div>
</div>
</x-layout>
