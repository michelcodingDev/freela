<x-layout title="Dashboard - FreelaJob">

    @php
        $user = auth()->user();
        $isCliente = $user->isCliente();
        $isFreelancer = $user->isFreelancer();
        $isAdmin = $user->isAdmin();
    @endphp

    <!-- Header da Página -->
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Meu Dashboard</h1>
            <p class="text-gray-600">Bem-vindo de volta, {{ $user->name }}!</p>
        </div>

        @if ($isCliente)
            <a href="{{ route('projects.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2 transition-all shadow-lg">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Novo Projeto
            </a>
        @endif
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @if ($isCliente)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Projetos Ativos</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <i data-lucide="briefcase" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Propostas Recebidas</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                        <i data-lucide="file-text" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Em Custódia</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ 0,00</h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                        <i data-lucide="credit-card" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Concluídos</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        @elseif ($isFreelancer)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Saldo Disponível</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ 0,00</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                        <i data-lucide="wallet" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Bloqueado (Custódia)</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ 0,00</h3>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                        <i data-lucide="lock" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Avaliação Média</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">0.0</h3>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center text-yellow-600">
                        <i data-lucide="star" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Projetos Concluídos</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                        <i data-lucide="award" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        @elseif ($isAdmin)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Usuários</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Projetos Ativos</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">0</h3>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                        <i data-lucide="briefcase" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">GMV Total</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ 0,00</h3>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                        <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Receita Plataforma</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ 0,00</h3>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <i data-lucide="credit-card" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Conteúdo específico por tipo de usuário -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Sidebar para cliente/freelancer -->
        @if ($isCliente || $isFreelancer)
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Minhas Ações Rápidas</h3>

                    @if ($isCliente)
                        <div class="space-y-3">
                            <a href="{{ route('projects.create') }}"
                               class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-xl text-blue-700 font-medium transition-all border border-blue-100">
                                <i data-lucide="file-text" class="w-4 h-4"></i>
                                Publicar Projeto
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-green-50 hover:bg-green-100 rounded-xl text-green-700 font-medium transition-all border border-green-100">
                                <i data-lucide="check" class="w-4 h-4"></i>
                                Ver Propostas
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-amber-50 hover:bg-amber-100 rounded-xl text-amber-700 font-medium transition-all border border-amber-100">
                                <i data-lucide="credit-card" class="w-4 h-4"></i>
                                Pagamentos
                            </a>
                        </div>
                    @elseif ($isFreelancer)
                        <div class="space-y-3">
                            <a href="{{ route('projects.index') }}"
                               class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-xl text-blue-700 font-medium transition-all border border-blue-100">
                                <i data-lucide="search" class="w-4 h-4"></i>
                                Buscar Projetos
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-green-50 hover:bg-green-100 rounded-xl text-green-700 font-medium transition-all border border-green-100">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                Minhas Propostas
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-xl text-purple-700 font-medium transition-all border border-purple-100">
                                <i data-lucide="wallet" class="w-4 h-4"></i>
                                Solicitar Saque
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Perfil resumido -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Meu Perfil</h3>
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                             class="w-16 h-16 rounded-full object-cover border-2 border-gray-100" alt="Foto">
                        <div>
                            <p class="font-bold text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 capitalize">{{ $user->type }}</p>
                            @if ($user->profile?->professional_title)
                                <p class="text-sm text-green-600 font-medium">{{ $user->profile->professional_title }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('profile') }}" class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg text-center text-sm font-medium transition-all">
                            Editar Perfil →
                        </a>
                    </div>
                </div>
            </div>
        @endif

        <!-- Conteúdo principal -->
        <div class="@if ($isCliente || $isFreelancer) lg:col-span-2 @endif space-y-8">
            @if ($isAdmin)
                <!-- Admin: resumo geral -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Visão Geral da Plataforma</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500">Últimos usuários cadastrados</p>
                            <div class="space-y-3 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Nenhum usuário recente</span>
                                    <span class="text-xs text-gray-400">--</span>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500">Projetos recentes</p>
                            <div class="space-y-3 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Nenhum projeto recente</span>
                                    <span class="text-xs text-gray-400">--</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Ações Rápidas Admin</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 p-4 bg-blue-50 rounded-xl border border-blue-100 text-blue-700 font-medium hover:bg-blue-100 transition-all">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            Gerenciar Usuários
                        </a>
                        <a href="{{ route('admin.payments') }}" class="flex items-center gap-3 p-4 bg-green-50 rounded-xl border border-green-100 text-green-700 font-medium hover:bg-green-100 transition-all">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            Ver Pagamentos
                        </a>
                        <a href="{{ route('admin.plans') }}" class="flex items-center gap-3 p-4 bg-purple-50 rounded-xl border border-purple-100 text-purple-700 font-medium hover:bg-purple-100 transition-all">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            Gerenciar Planos
                        </a>
                        <a href="{{ route('admin.disputes') }}" class="flex items-center gap-3 p-4 bg-amber-50 rounded-xl border border-amber-100 text-amber-700 font-medium hover:bg-amber-100 transition-all">
                            <i data-lucide="alert-circle" class="w-4 h-4"></i>
                            Disputas
                        </a>
                    </div>
                </div>
            @elseif ($isCliente)
                <!-- Cliente: projetos recentes -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Meus Projetos</h3>
                    <div class="text-center py-12 text-gray-400">
                        <i data-lucide="briefcase" class="w-8 h-8 mx-auto mb-3 opacity-50"></i>
                        <p class="text-sm">Você ainda não publicou nenhum projeto.</p>
                        <a href="{{ route('projects.create') }}" class="mt-4 inline-block text-green-600 font-medium hover:text-green-700">
                            Criar primeiro projeto →
                        </a>
                    </div>
                </div>
            @elseif ($isFreelancer)
                <!-- Freelancer: projetos recomendados -->
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Projetos Recomendados</h3>
                    <div class="text-center py-12 text-gray-400">
                        <i data-lucide="search" class="w-8 h-8 mx-auto mb-3 opacity-50"></i>
                        <p class="text-sm">Nenhum projeto disponível no momento.</p>
                        <a href="{{ route('projects.index') }}" class="mt-4 inline-block text-green-600 font-medium hover:text-green-700">
                            Ver todos os projetos →
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar para admin -->
        @if ($isAdmin)
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Painel Administrativo</h3>
                    <div class="space-y-3">
                        <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-xl text-blue-700 font-medium transition-all border border-blue-100">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            Usuários
                        </a>
                        <a href="{{ route('admin.payments') }}" class="flex items-center gap-3 px-4 py-3 bg-green-50 hover:bg-green-100 rounded-xl text-green-700 font-medium transition-all border border-green-100">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            Pagamentos
                        </a>
                        <a href="{{ route('admin.projects') }}" class="flex items-center gap-3 px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-xl text-purple-700 font-medium transition-all border border-purple-100">
                            <i data-lucide="briefcase" class="w-4 h-4"></i>
                            Projetos
                        </a>
                        <a href="{{ route('admin.plans') }}" class="flex items-center gap-3 px-4 py-3 bg-amber-50 hover:bg-amber-100 rounded-xl text-amber-700 font-medium transition-all border border-amber-100">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                            Planos
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>

</x-layout>
