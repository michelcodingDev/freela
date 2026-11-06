<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'FreelaJob' }} - Plataforma Freelancer</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js para interatividade -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x/dist/cdn.min.js"></script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        [x-cloak] { display: none !important; }
        .glass-panel {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .gradient-text {
            background: linear-gradient(135deg, #1e3a5f 0%, #16a34a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .badge-status {
            @apply inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium;
        }
        .badge-ativo { @apply badge-status bg-green-100 text-green-700; }
        .badge-inativo { @apply badge-status bg-gray-100 text-gray-700; }
        .badge-suspenso { @apply badge-status bg-amber-100 text-amber-700; }
        .badge-banido { @apply badge-status bg-red-100 text-red-700; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-3 shrink-0">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-700 to-green-600 rounded-lg flex items-center justify-center text-white font-bold">
                        <i data-lucide="zap" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">Freela<span class="text-green-600">Job</span></span>
                </a>

                <div class="hidden md:flex items-center gap-6">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Início</a>
                    <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Projetos</a>
                    <a href="{{ route('freelancers.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Freelancers</a>
                    <a href="{{ route('categories.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Categorias</a>
                    <a href="{{ route('ranking.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Ranking</a>
                </div>

                <div class="flex items-center gap-3">
                    <button type="button" @click="mobileOpen = !mobileOpen" class="md:hidden p-2 text-gray-600 hover:text-green-600 rounded-lg" aria-label="Abrir menu">
                        <i data-lucide="menu" class="w-6 h-6"></i>
                    </button>

                    @auth
                        <a href="{{ route('notifications.index') }}" class="relative p-2 text-gray-500 hover:text-green-600 hover:bg-gray-50 rounded-lg transition" title="Notificações">
                            <i data-lucide="bell" class="w-5 h-5"></i>
                            @php $unreadNotifications = auth()->user()->appNotifications()->where('is_read', false)->count(); @endphp
                            @if ($unreadNotifications > 0)
                                <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-[10px] leading-none px-1.5 py-1 rounded-full font-bold">{{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}</span>
                            @endif
                        </a>

                        <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition shadow-lg">
                            Meu Dashboard
                        </a>

                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button @click="open = !open" class="flex items-center gap-2 rounded-full focus:outline-none focus:ring-2 focus:ring-green-500">
                                <img src="{{ auth()->user()->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=random' }}"
                                     class="w-10 h-10 rounded-full border-2 border-gray-100 object-cover"
                                     alt="Perfil">
                                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 hidden sm:block"></i>
                            </button>

                            <div x-show="open" x-transition x-cloak class="absolute right-0 mt-2 w-60 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                                <div class="px-4 py-2 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
                                </a>
                                @if (auth()->user()->isCliente() || auth()->user()->isFreelancer())
                                    <a href="{{ route('projects.mine') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i data-lucide="folder-kanban" class="w-4 h-4"></i> Meus projetos
                                    </a>
                                @endif
                                <a href="{{ route('wallet.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i data-lucide="wallet" class="w-4 h-4"></i> Carteira e extrato
                                </a>
                                @if (auth()->user()->isFreelancer())
                                    <a href="{{ route('proposals.mine') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i data-lucide="file-text" class="w-4 h-4"></i> Minhas propostas
                                    </a>
                                    <a href="{{ route('invites.index') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i data-lucide="mail" class="w-4 h-4"></i> Convites recebidos
                                    </a>
                                @endif
                                <a href="{{ route('profile.show') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                    <i data-lucide="user" class="w-4 h-4"></i> Meu perfil
                                </a>
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">
                                        <i data-lucide="shield" class="w-4 h-4"></i> Painel admin
                                    </a>
                                @endif
                                <form method="POST" action="{{ route('logout') }}" class="border-t border-gray-100 mt-1 pt-1">
                                    @csrf
                                    <button class="w-full flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <i data-lucide="log-out" class="w-4 h-4"></i> Sair
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Entrar</a>
                        <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-medium transition shadow-lg">Cadastrar</a>
                    @endauth
                </div>
            </div>

            <!-- Menu mobile -->
            <div x-show="mobileOpen" x-cloak x-transition class="md:hidden border-t border-gray-100 py-3 space-y-1">
                <a href="{{ route('home') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Início</a>
                <a href="{{ route('projects.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Projetos</a>
                <a href="{{ route('freelancers.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Freelancers</a>
                <a href="{{ route('categories.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Categorias</a>
                <a href="{{ route('ranking.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Ranking</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Meu Dashboard</a>
                    @if (auth()->user()->isCliente() || auth()->user()->isFreelancer())
                        <a href="{{ route('projects.mine') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Meus Projetos</a>
                    @endif
                    <a href="{{ route('wallet.index') }}" class="block px-3 py-2 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Carteira</a>
                @endauth
            </div>
        </div>

        @auth
            @if (auth()->user()->isAdmin())
                <div class="border-t border-gray-100 bg-gray-50/80">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex items-center gap-1 overflow-x-auto py-1.5 text-sm">
                            @php
                                $adminLinks = [
                                    ['route' => 'admin.dashboard', 'label' => 'Dashboard', 'active' => 'admin.dashboard'],
                                    ['route' => 'admin.users', 'label' => 'Usuários', 'active' => 'admin.users*'],
                                    ['route' => 'admin.projects', 'label' => 'Projetos', 'active' => 'admin.projects*'],
                                    ['route' => 'admin.payments', 'label' => 'Pagamentos', 'active' => 'admin.payments*'],
                                    ['route' => 'admin.disputes', 'label' => 'Disputas', 'active' => 'admin.disputes*'],
                                    ['route' => 'admin.withdrawals.index', 'label' => 'Saques', 'active' => 'admin.withdrawals*'],
                                    ['route' => 'admin.subscriptions.index', 'label' => 'Assinaturas', 'active' => 'admin.subscriptions*'],
                                    ['route' => 'admin.plans.index', 'label' => 'Planos', 'active' => 'admin.plans*'],
                                    ['route' => 'admin.categories.index', 'label' => 'Categorias', 'active' => 'admin.categories*'],
                                    ['route' => 'admin.skills.index', 'label' => 'Habilidades', 'active' => 'admin.skills*'],
                                    ['route' => 'admin.logs.index', 'label' => 'Auditoria', 'active' => 'admin.logs*'],
                                ];
                            @endphp
                            @foreach ($adminLinks as $link)
                                <a href="{{ route($link['route']) }}"
                                   class="whitespace-nowrap px-3 py-1.5 rounded-lg font-medium transition {{ request()->routeIs($link['active']) ? 'bg-blue-600 text-white' : 'text-gray-600 hover:bg-white hover:text-blue-700' }}">
                                    {{ $link['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        @endauth
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        @if (session('success'))
            <div class="mt-4 bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if (session('info'))
            <div class="mt-4 bg-blue-50 border border-blue-200 text-blue-800 px-6 py-4 rounded-lg flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <i data-lucide="info" class="w-5 h-5"></i>
                <p>{{ session('info') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-4 bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @auth
            @if (! auth()->user()->hasVerifiedEmail())
                <div class="mt-4 bg-amber-50 border border-amber-200 text-amber-800 px-6 py-4 rounded-lg flex flex-col sm:flex-row sm:items-center gap-3">
                    <i data-lucide="mail-warning" class="w-5 h-5 shrink-0"></i>
                    <p class="text-sm">
                        Confirme seu e-mail para liberar todos os recursos da conta. Você pode continuar usando a plataforma enquanto isso.
                    </p>
                    <a href="{{ route('verification.notice') }}" class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-semibold">
                        Confirmar agora
                    </a>
                </div>
            @endif
        @endauth
    </div>

    <!-- Conteúdo Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row gap-3 justify-between items-center text-gray-500 text-sm">
            <p>&copy; {{ now()->year }} FreelaJob. Todos os direitos reservados.</p>
            <div class="flex flex-wrap justify-center gap-x-6 gap-y-2">
                <a href="{{ route('pages.terms') }}" class="hover:text-gray-900">Termos de Uso</a>
                <a href="{{ route('pages.privacy') }}" class="hover:text-gray-900">Privacidade</a>
                <a href="{{ route('pages.support') }}" class="hover:text-gray-900">Suporte</a>
                <a href="{{ route('freelancers.index') }}" class="hover:text-gray-900">Freelancers</a>
                <a href="{{ route('ranking.index') }}" class="hover:text-gray-900">Ranking</a>
                <a href="{{ route('projects.index') }}" class="hover:text-gray-900">Projetos</a>
            </div>
        </div>
    </footer>

    <script>
        // Inicializar Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
