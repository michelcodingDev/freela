<x-layout title="Editar Perfil - FreelaJob">

    @php
        $user = auth()->user()->load('profile');
        $profile = $user->profile;
    @endphp

    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Editar Perfil</h1>
                    <p class="text-gray-600 text-sm">Atualize suas informações pessoais e profissionais.</p>
                </div>
                <a href="{{ route('profile.show') }}"
                   class="text-sm text-green-600 hover:text-green-700 font-medium">
                    ← Voltar ao perfil
                </a>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nome completo *</label>
                        <input type="text" name="name" id="name"
                               value="{{ old('name', $user->name) }}"
                               required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        @error('name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail *</label>
                        <input type="email" name="email" id="email"
                               value="{{ old('email', $user->email) }}"
                               required
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                        <p class="text-xs text-gray-400 mt-1">Ao trocar o e-mail, enviamos uma nova confirmação.</p>
                        @error('email')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="tel" name="phone" id="phone"
                               value="{{ old('phone', $profile?->phone) }}"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="(11) 99999-9999">
                    </div>

                    <div>
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-1">Cidade</label>
                        <input type="text" name="city" id="city"
                               value="{{ old('city', $profile?->city) }}"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="São Paulo">
                    </div>

                    <div>
                        <label for="state" class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                        <input type="text" name="state" id="state"
                               value="{{ old('state', $profile?->state) }}"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="SP">
                    </div>

                    <div>
                        <label for="zip_code" class="block text-sm font-medium text-gray-700 mb-1">CEP</label>
                        <input type="text" name="zip_code" id="zip_code"
                               value="{{ old('zip_code', $profile?->zip_code) }}"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                               placeholder="01234-567">
                    </div>

                    <div class="md:col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio / Sobre você</label>
                        <textarea name="bio" id="bio" rows="4"
                                  class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                  placeholder="Conte um pouco sobre você...">{{ old('bio', $profile?->bio) }}</textarea>
                    </div>
                </div>

                <!-- Seção para freelancer -->
                @if ($user->isFreelancer())
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-6">Informações Profissionais</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="professional_title" class="block text-sm font-medium text-gray-700 mb-1">Título Profissional</label>
                                <input type="text" name="professional_title" id="professional_title"
                                       value="{{ old('professional_title', $profile?->professional_title) }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="Ex: Desenvolvedor React Sênior">
                            </div>

                            <div>
                                <label for="pix_key" class="block text-sm font-medium text-gray-700 mb-1">Chave Pix (para recebimentos)</label>
                                <input type="text" name="pix_key" id="pix_key"
                                       value="{{ old('pix_key', $profile?->pix_key) }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="ex: email@exemplo.com ou CPF">
                            </div>

                            <div>
                                <label for="bank_name" class="block text-sm font-medium text-gray-700 mb-1">Nome do Banco</label>
                                <input type="text" name="bank_name" id="bank_name"
                                       value="{{ old('bank_name', $profile?->bank_name) }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="Banco do Brasil">
                            </div>

                            <div>
                                <label for="bank_account_type" class="block text-sm font-medium text-gray-700 mb-1">Tipo de Conta</label>
                                <select name="bank_account_type" id="bank_account_type"
                                        class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm">
                                    <option value="corrente" {{ old('bank_account_type', $profile?->bank_account_type) == 'corrente' ? 'selected' : '' }}>Conta Corrente</option>
                                    <option value="poupanca" {{ old('bank_account_type', $profile?->bank_account_type) == 'poupanca' ? 'selected' : '' }}>Conta Poupança</option>
                                </select>
                            </div>

                            <div>
                                <label for="bank_agency" class="block text-sm font-medium text-gray-700 mb-1">Agência</label>
                                <input type="text" name="bank_agency" id="bank_agency"
                                       value="{{ old('bank_agency', $profile?->bank_agency) }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="1234">
                            </div>

                            <div>
                                <label for="bank_account" class="block text-sm font-medium text-gray-700 mb-1">Conta</label>
                                <input type="text" name="bank_account" id="bank_account"
                                       value="{{ old('bank_account', $profile?->bank_account) }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="54321">
                            </div>

                            <div>
                                <label for="bank_account_digit" class="block text-sm font-medium text-gray-700 mb-1">Dígito</label>
                                <input type="text" name="bank_account_digit" id="bank_account_digit"
                                       value="{{ old('bank_account_digit', $profile?->bank_account_digit) }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="1">
                            </div>

                            <div>
                                <label for="cpf_cnpj" class="block text-sm font-medium text-gray-700 mb-1">CPF/CNPJ</label>
                                <input type="text" name="cpf_cnpj" id="cpf_cnpj"
                                       value="{{ old('cpf_cnpj', $profile?->cpf_cnpj) }}"
                                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition-all text-sm"
                                       placeholder="123.456.789-00">
                            </div>
                        </div>
                    </div>
                @endif

                @if ($user->isFreelancer())
                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Habilidades</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($skills ?? [] as $skill)
                                <label class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 border border-gray-200 rounded-full text-sm cursor-pointer">
                                    <input type="checkbox" name="skills[]" value="{{ $skill->id }}" {{ in_array($skill->id, old('skills', $user->skills->pluck('id')->all())) ? 'checked' : '' }}>
                                    {{ $skill->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Foto de perfil -->
                <div class="border-t border-gray-100 pt-6">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Foto de Perfil</h3>
                    <div class="flex items-center gap-4">
                        <div class="w-24 h-24">
                            <img src="{{ $profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                 class="w-full h-full rounded-full object-cover border-2 border-gray-100" alt="Foto atual">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Enviar nova foto</label>
                            <input type="file" name="profile_photo" id="profile_photo"
                                   accept="image/*"
                                   class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <p class="text-xs text-gray-500 mt-1">Máximo 2MB. Formatos: JPG, PNG.</p>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-gray-100 flex justify-end">
                    <button type="submit"
                            class="px-8 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-all shadow-lg transform active:scale-95 flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Salvar Alterações
                    </button>
                </div>
            </form>

            <!-- Segurança: alterar senha -->
            <div class="border-t border-gray-100 pt-6 mt-8">
                <h3 class="text-lg font-bold text-gray-900">Segurança</h3>
                <p class="text-sm text-gray-500 mt-1">Altere sua senha. Outras sessões serão encerradas.</p>

                <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4 mt-5">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Senha atual *</label>
                        <input type="password" name="current_password" id="current_password" required autocomplete="current-password"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                        @error('current_password')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Nova senha *</label>
                            <input type="password" name="password" id="password" required minlength="8" autocomplete="new-password"
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                            @error('password')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirmar nova senha *</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required minlength="8" autocomplete="new-password"
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none text-sm">
                        </div>
                    </div>

                    <button type="submit"
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-sm flex items-center gap-2">
                        <i data-lucide="key-round" class="w-4 h-4"></i>
                        Alterar senha
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-layout>
