<x-layout title="Dashboard - FreelaJob">
    @php
        $user = $user ?? auth()->user();
        $stats = $stats ?? [];
        $isCliente = $user->isCliente();
        $isFreelancer = $user->isFreelancer();
        $isAdmin = $user->isAdmin();
    @endphp

    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Meu Dashboard</h1>
            <p class="text-gray-600">Bem-vindo de volta, {{ $user->name }}!</p>
        </div>
        @if ($isCliente)
            <a href="{{ route('projects.create') }}" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-xl font-semibold flex items-center gap-2 shadow-lg">
                <i data-lucide="plus" class="w-4 h-4"></i> Novo Projeto
            </a>
        @endif
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        @if ($isCliente)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Projetos Ativos</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['activeProjects'] ?? 0 }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Propostas Recebidas</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['receivedProposals'] ?? 0 }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Em Custódia</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($stats['escrowTotal'] ?? 0, 2, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Concluídos</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completedProjects'] ?? 0 }}</h3>
            </div>
        @elseif ($isFreelancer)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Saldo Disponível</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($stats['availableBalance'] ?? 0, 2, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Bloqueado (Custódia)</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($stats['lockedBalance'] ?? 0, 2, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Avaliação Média</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['averageRating'] ?? 0, 1) }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Projetos Concluídos</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['completedProjects'] ?? 0 }}</h3>
            </div>
        @elseif ($isAdmin)
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Total Usuários</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['totalUsers'] ?? 0 }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Projetos Ativos</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ $stats['activeProjects'] ?? 0 }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">GMV Total</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($stats['gmv'] ?? 0, 2, ',', '.') }}</h3>
            </div>
            <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                <p class="text-sm text-gray-500 font-medium">Receita Plataforma</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($stats['platformRevenue'] ?? 0, 2, ',', '.') }}</h3>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @if ($isCliente || $isFreelancer)
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Ações Rápidas</h3>
                    <div class="space-y-3">
                        @if ($isCliente)
                            <a href="{{ route('projects.create') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-xl text-blue-700 font-medium border border-blue-100">Publicar Projeto</a>
                            <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl text-emerald-700 font-medium border border-emerald-100">Carteira e Extrato</a>
                            <a href="{{ route('disputes.index') }}" class="flex items-center gap-3 px-4 py-3 bg-amber-50 hover:bg-amber-100 rounded-xl text-amber-700 font-medium border border-amber-100">Disputas</a>
                        @elseif ($isFreelancer)
                            <a href="{{ route('projects.index') }}" class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-xl text-blue-700 font-medium border border-blue-100">Buscar Projetos</a>
                            <a href="{{ route('wallet.index') }}" class="flex items-center gap-3 px-4 py-3 bg-emerald-50 hover:bg-emerald-100 rounded-xl text-emerald-700 font-medium border border-emerald-100">Carteira e Extrato</a>
                            <a href="{{ route('withdrawals.index') }}" class="flex items-center gap-3 px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-xl text-purple-700 font-medium border border-purple-100">Solicitar Saque</a>
                            <a href="{{ route('portfolio.index') }}" class="flex items-center gap-3 px-4 py-3 bg-green-50 hover:bg-green-100 rounded-xl text-green-700 font-medium border border-green-100">Portfólio</a>
                        @endif
                        <a href="{{ route('notifications.index') }}" class="flex items-center gap-3 px-4 py-3 bg-gray-50 hover:bg-gray-100 rounded-xl text-gray-700 font-medium border border-gray-100">
                            Notificações @if(($stats['unreadNotifications'] ?? 0) > 0) <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full">{{ $stats['unreadNotifications'] }}</span> @endif
                        </a>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Meu Perfil</h3>
                    <div class="flex items-center gap-4">
                        <img src="{{ $user->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}" class="w-16 h-16 rounded-full object-cover border-2 border-gray-100" alt="Foto">
                        <div>
                            <p class="font-bold text-gray-900">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 capitalize">{{ $user->type }}</p>
                            @if ($user->profile?->professional_title)
                                <p class="text-sm text-green-600 font-medium">{{ $user->profile->professional_title }}</p>
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('profile.show') }}" class="mt-4 block py-2.5 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg text-center text-sm font-medium">Editar Perfil →</a>
                </div>
            </div>
        @endif

        <div class="@if ($isCliente || $isFreelancer) lg:col-span-2 @endif space-y-8">
            @if ($isAdmin)
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Visão Geral</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500">Últimos usuários</p>
                            @forelse ($stats['recentUsers'] ?? [] as $u)
                                <div class="flex justify-between py-2 text-sm"><span>{{ $u->name }}</span><span class="text-gray-400">{{ $u->created_at->format('d/m') }}</span></div>
                            @empty
                                <p class="text-sm text-gray-500 mt-2">Nenhum usuário</p>
                            @endforelse
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl">
                            <p class="text-sm text-gray-500">Projetos recentes</p>
                            @forelse ($stats['recentProjects'] ?? [] as $p)
                                <div class="flex justify-between py-2 text-sm"><span class="truncate">{{ $p->title }}</span><span class="text-gray-400 capitalize">{{ $p->status }}</span></div>
                            @empty
                                <p class="text-sm text-gray-500 mt-2">Nenhum projeto</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @elseif ($isCliente)
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Meus Projetos</h3>
                    @forelse ($stats['recentProjects'] ?? [] as $p)
                        <a href="{{ route('projects.show', $p) }}" class="flex justify-between py-3 border-b border-gray-100 last:border-0">
                            <span class="text-sm font-medium">{{ $p->title }}</span>
                            <span class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $p->status) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">Você ainda não publicou nenhum projeto.</p>
                    @endforelse
                </div>
            @elseif ($isFreelancer)
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Projetos em Andamento</h3>
                    @forelse ($stats['recentProjects'] ?? [] as $p)
                        <a href="{{ route('projects.show', $p) }}" class="flex justify-between py-3 border-b border-gray-100 last:border-0">
                            <span class="text-sm font-medium">{{ $p->title }}</span>
                            <span class="text-xs text-gray-500 capitalize">{{ str_replace('_', ' ', $p->status) }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">Nenhum projeto em andamento.</p>
                    @endforelse
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Recomendados</h3>
                    @forelse ($stats['recommendedProjects'] ?? [] as $p)
                        <a href="{{ route('projects.show', $p) }}" class="flex justify-between py-3 border-b border-gray-100 last:border-0">
                            <span class="text-sm font-medium">{{ $p->title }}</span>
                            <span class="text-xs text-gray-500">{{ $p->category->name ?? '' }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-400">Nenhum projeto recomendado.</p>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</x-layout>
