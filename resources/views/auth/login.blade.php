<x-layout title="Entrar - FreelaJob">

    <div class="flex min-h-[calc(100vh-200px)] items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-md">
            <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="zap" class="w-8 h-8 text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Bem-vindo de volta</h2>
                    <p class="text-gray-600 text-sm mt-2">Entre na sua conta para continuar</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-4" x-data="{ showPassword: false }">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email') }}"
                               required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="seu@email.com">
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="relative">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                        <input type="password" name="password" id="password"
                               required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm pr-10"
                               placeholder="••••••••">
                        <button type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-3 text-gray-400 hover:text-gray-600">
                            <i data-lucide="@showPassword ? 'eye-off' : 'eye'" class="w-4 h-4"></i>
                        </button>
                        @error('password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" type="checkbox" name="remember"
                                   class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500">
                            <label for="remember-me" class="ml-2 text-sm text-gray-600">Lembrar de mim</label>
                        </div>
                        <a href="{{ route('password.request') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">Esqueceu a senha?</a>
                    </div>

                    <button type="submit"
                            class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-all shadow-lg transform active:scale-95">
                        Entrar
                    </button>
                </form>

                <div class="mt-6 relative">
                    <div class="relative flex items-center justify-center">
                        <span class="absolute inset-0 flex items-center">
                            <span class="w-full border-t border-gray-200"></span>
                        </span>
                        <span class="px-4 text-xs font-medium text-gray-500 bg-white">Ou continue com</span>
                    </div>

                    <div class="flex gap-4 mt-4">
                        <a href="#" class="w-1/2 flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium transition-all">
                            <i data-lucide="google" class="w-4 h-4 mr-2"></i>
                            Google
                        </a>
                        <a href="#" class="w-1/2 flex items-center justify-center px-4 py-2.5 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 text-gray-700 text-sm font-medium transition-all">
                            <i data-lucide="github" class="w-4 h-4 mr-2"></i>
                            GitHub
                        </a>
                    </div>
                </div>

                <div class="mt-8 text-center text-sm text-gray-600">
                    Não tem uma conta?
                    <a href="{{ route('register') }}" class="font-medium text-green-600 hover:text-green-700">Cadastre-se</a>
                </div>
            </div>
        </div>
    </div>

</x-layout>
