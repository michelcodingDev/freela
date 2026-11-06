<x-layout title="Meu Perfil - FreelaJob">

    @php
        $user = auth()->user()->load('profile');
        $profile = $user->profile;
    @endphp

    <div class="max-w-3xl mx-auto">
        <!-- Header do perfil -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <div class="flex flex-col md:flex-row md:items-center gap-6 mb-6">
                <div class="w-24 h-24 md:w-32 md:h-32">
                    <img src="{{ $profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                         class="w-full h-full rounded-full object-cover border-4 border-gray-100" alt="Foto de perfil">
                </div>

                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                    <p class="text-gray-500 capitalize text-sm">{{ $user->type }}</p>

                    @if ($profile?->identity_verified)
                        <span class="flex items-center gap-2 text-green-600 text-sm font-medium mt-2">
                            <i data-lucide="shield-check" class="w-4 h-4"></i>
                            Identidade Verificada
                        </span>
                    @endif
                </div>

                <div class="flex gap-3">
                    <a href="{{ route('profile.edit') }}"
                       class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all">
                        Editar Perfil
                    </a>
                    <a href="{{ route('logout') }}" method="POST" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       class="px-5 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-xl font-medium transition-all">
                        Sair
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

            <!-- Informações -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                <div class="bg-gray-50 p-6 rounded-xl">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Informações Pessoais</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">E-mail</span>
                            <span class="text-sm text-gray-900">{{ $user->email }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Telefone</span>
                            <span class="text-sm text-gray-900">{{ $profile?->phone ?? 'Não informado' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">Cidade / Estado</span>
                            <span class="text-sm text-gray-900">{{ $profile?->city ?? 'Não informado' }} / {{ $profile?->state ?? 'Não informado' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">CEP</span>
                            <span class="text-sm text-gray-900">{{ $profile?->zip_code ?? 'Não informado' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600 text-sm">CPF/CNPJ</span>
                            <span class="text-sm text-gray-900">{{ $profile?->cpf_cnpj ?? 'Não informado' }}</span>
                        </div>
                    </div>
                </div>

                @if ($user->isFreelancer())
                    <div class="bg-gray-50 p-6 rounded-xl">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Informações Profissionais</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Título Profissional</span>
                                <span class="text-sm text-gray-900">{{ $profile?->professional_title ?? 'Não informado' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Chave Pix</span>
                                <span class="text-sm text-gray-900">{{ $profile?->pix_key ?? 'Não informado' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Banco</span>
                                <span class="text-sm text-gray-900">{{ $profile?->bank_name ?? 'Não informado' }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Agência / Conta</span>
                                <span class="text-sm text-gray-900">{{ $profile?->bank_agency ?? '-' }} / {{ $profile?->bank_account ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Estatísticas</h3>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-center bg-white p-4 rounded-lg border border-gray-100">
                                    <div class="text-2xl font-bold text-blue-600">{{ $profile?->projects_completed ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">Projetos Concluídos</div>
                                </div>
                                <div class="text-center bg-white p-4 rounded-lg border border-gray-100">
                                    <div class="text-2xl font-bold text-green-600">{{ number_format($profile?->rating_average ?? 0, 1) }}</div>
                                    <div class="text-xs text-gray-500">Avaliação Média</div>
                                </div>
                                <div class="text-center bg-white p-4 rounded-lg border border-gray-100">
                                    <div class="text-2xl font-bold text-purple-600">{{ $profile?->total_reviews ?? 0 }}</div>
                                    <div class="text-xs text-gray-500">Avaliações</div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif ($user->isCliente())
                    <div class="bg-gray-50 p-6 rounded-xl">
                        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Informações da Empresa</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600 text-sm">Cidade</span>
                                <span class="text-sm text-gray-900">{{ $profile?->city ?? 'Não informado' }}</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Bio -->
            @if ($profile?->bio)
                <div class="mt-8">
                    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Sobre Mim</h3>
                    <p class="text-gray-700 leading-relaxed bg-gray-50 p-4 rounded-lg border border-gray-100">{{ $profile->bio }}</p>
                </div>
            @endif
        </div>

        <!-- Ações rápidas -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Ações Rápidas</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @if ($user->isCliente())
                    <a href="{{ route('projects.create') }}"
                       class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100 text-blue-700 font-medium hover:bg-blue-100 transition-all group">
                        <i data-lucide="file-text" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
                        <span>Novo Projeto</span>
                    </a>
                    <a href="{{ route('projects.index') }}"
                       class="flex items-center gap-3 p-4 bg-green-50 rounded-xl border border-green-100 text-green-700 font-medium hover:bg-green-100 transition-all group">
                        <i data-lucide="search" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
                        <span>Buscar Projetos</span>
                    </a>
                @elseif ($user->isFreelancer())
                    <a href="{{ route('projects.index') }}"
                       class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100 text-blue-700 font-medium hover:bg-blue-100 transition-all group">
                        <i data-lucide="search" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
                        <span>Buscar Projetos</span>
                    </a>
                    <a href="{{ route('withdrawals.index') }}"
                       class="flex items-center gap-3 p-4 bg-green-50 rounded-xl border border-green-100 text-green-700 font-medium hover:bg-green-100 transition-all group">
                        <i data-lucide="wallet" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
                        <span>Solicitar Saque</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

</x-layout>
