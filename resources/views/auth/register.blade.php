<x-layout title="Cadastro - FreelaJob">

    <div class="flex min-h-[calc(100vh-200px)] items-center justify-center bg-gray-50 px-4">
        <div class="w-full max-w-2xl">
            <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-100">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-green-600 rounded-xl flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="user-plus" class="w-8 h-8 text-white"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Crie sua conta</h2>
                    <p class="text-gray-600 text-sm mt-2">Junte-se à maior plataforma de freelancers</p>
                </div>

                <form method="POST" action="{{ route('register.post') }}" class="space-y-4" x-data="{ selectedType: 'cliente' }">
                    @csrf

                    <!-- Tipo de usuário -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">Tipo de conta</label>
                        <div class="flex gap-2" x-bind:class="{'border-2 border-green-500': selectedType === 'cliente', 'border-2 border-blue-500': selectedType === 'freelancer'}">
                            <label class="flex-1 cursor-pointer p-4 rounded-xl border border-gray-200 text-center hover:bg-gray-50 transition-all group"
                                   @click="selectedType = 'cliente'"
                                   x-bind:class="{'bg-green-50': selectedType === 'cliente', 'bg-blue-50': selectedType === 'freelancer'}">
                                <input type="radio" name="type" value="cliente"
                                       x-model="selectedType"
                                       class="sr-only">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="briefcase" class="w-6 h-6 text-gray-400 group-hover:text-green-600 transition-colors" x-bind:class="{'text-green-600': selectedType === 'cliente', 'text-blue-600': selectedType === 'freelancer'}"></i>
                                    <span class="text-sm font-medium text-gray-900" x-bind:class="{'text-green-600': selectedType === 'cliente', 'text-blue-600': selectedType === 'freelancer'}">Cliente</span>
                                    <span class="text-xs text-gray-500">Contratar profissionais</span>
                                </div>
                            </label>

                            <label class="flex-1 cursor-pointer p-4 rounded-xl border border-gray-200 text-center hover:bg-gray-50 transition-all group"
                                   @click="selectedType = 'freelancer'"
                                   x-bind:class="{'bg-green-50': selectedType === 'cliente', 'bg-blue-50': selectedType === 'freelancer'}">
                                <input type="radio" name="type" value="freelancer"
                                       x-model="selectedType"
                                       class="sr-only">
                                <div class="flex flex-col items-center gap-2">
                                    <i data-lucide="laptop" class="w-6 h-6 text-gray-400 group-hover:text-blue-600 transition-colors" x-bind:class="{'text-green-600': selectedType === 'cliente', 'text-blue-600': selectedType === 'freelancer'}"></i>
                                    <span class="text-sm font-medium text-gray-900" x-bind:class="{'text-green-600': selectedType === 'cliente', 'text-blue-600': selectedType === 'freelancer'}">Freelancer</span>
                                    <span class="text-xs text-gray-500">Oferecer seus serviços</span>
                                </div>
                            </label>
                        </div>
                        <input type="hidden" name="type" x-bind:value="selectedType">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
                            <input type="text" name="name" id="name"
                                   value="{{ old('name') }}"
                                   required
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                   placeholder="João Silva">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

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
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Senha</label>
                            <input type="password" name="password" id="password"
                                   required
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                   placeholder="••••••••">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar senha</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   required
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                   placeholder="••••••••">
                            @error('password_confirmation')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Campos específicos para freelancer -->
                    <div x-show="selectedType === 'freelancer'" x-transition class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Informações Profissionais</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="professional_title" class="block text-sm font-medium text-gray-700 mb-1">Título Profissional</label>
                                <input type="text" name="professional_title" id="professional_title"
                                       value="{{ old('professional_title') }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="Ex: Desenvolvedor React Sênior">
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                                <input type="text" name="city" id="city"
                                       value="{{ old('city') }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="São Paulo">
                            </div>
                        </div>

                        <div>
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio / Sobre você</label>
                            <textarea name="bio" id="bio" rows="3"
                                      class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                      placeholder="Conte um pouco sobre sua experiência e habilidades...">{{ old('bio') }}</textarea>
                        </div>

                        <div>
                            <label for="pix_key" class="block text-sm font-medium text-gray-700 mb-1">Chave Pix (para recebimentos)</label>
                            <input type="text" name="pix_key" id="pix_key"
                                   value="{{ old('pix_key') }}"
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                   placeholder="ex: email@exemplo.com ou CPF">
                        </div>
                    </div>

                    <!-- Campos específicos para cliente -->
                    <div x-show="selectedType === 'cliente'" x-transition class="mt-6 pt-6 border-t border-gray-100">
                        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wider mb-4">Informações da Empresa</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                                <input type="text" name="city" id="city"
                                       value="{{ old('city') }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="São Paulo">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                                <input type="tel" name="phone" id="phone"
                                       value="{{ old('phone') }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="(11) 99999-9999">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2 mb-4">
                        <input type="checkbox" name="terms" id="terms" required
                               class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500 mt-0.5">
                        <label for="terms" class="text-sm text-gray-600 leading-relaxed">
                            Li e aceito os <a href="#" class="text-green-600 hover:underline font-medium">Termos de Uso</a> e
                            <a href="#" class="text-green-600 hover:underline font-medium">Política de Privacidade</a>.
                        </label>
                    </div>

                    <button type="submit"
                            class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-all shadow-lg transform active:scale-95 flex items-center justify-center gap-2">
                        <i data-lucide="user-plus" class="w-5 h-5"></i>
                        Criar Conta
                    </button>
                </form>

                <div class="mt-8 text-center text-sm text-gray-600">
                    Já tem uma conta?
                    <a href="{{ route('login') }}" class="font-medium text-green-600 hover:text-green-700">Entrar</a>
                </div>
            </div>
        </div>
    </div>

</x-layout>
