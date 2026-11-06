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
<body class="bg-gray-50 min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gradient-to-br from-blue-700 to-green-600 rounded-lg flex items-center justify-center text-white font-bold">
                        <i data-lucide="zap" class="w-5 h-5"></i>
                    </div>
                    <span class="text-xl font-bold text-gray-900 tracking-tight">Freela<span class="text-green-600">Job</span></span>
                </div>

                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Início</a>
                    <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Projetos</a>
                    <a href="{{ route('projects.index') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Freelancers</a>

                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium transition shadow-lg">
                            Meu Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-green-600 font-medium transition">Entrar</a>
                        <a href="{{ route('register') }}" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-medium transition shadow-lg">Cadastrar</a>
                    @endauth
                </div>

                @auth
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-medium text-gray-900">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ auth()->user()->type }}</p>
                        </div>
                        <img src="{{ auth()->user()->profile?->profile_photo ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) . '&background=random' }}"
                             class="w-10 h-10 rounded-full border-2 border-gray-100 object-cover cursor-pointer"
                             alt="Perfil">
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-lg flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <ul class="list-disc list-inside mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @foreach (session('errors', []) as $error)
            <div class="bg-red-50 border border-red-200 text-red-800 px-6 py-4 rounded-lg flex items-center gap-3 animate-in fade-in slide-in-from-top-2 duration-300">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
                <p>{{ $error }}</p>
            </div>
        @endforeach
    </div>

    <!-- Conteúdo Principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-auto py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center text-gray-500 text-sm">
            <p>&copy; {{ now()->year }} FreelaJob. Todos os direitos reservados.</p>
            <div class="flex gap-6">
                <a href="#" class="hover:text-gray-900">Termos de Uso</a>
                <a href="#" class="hover:text-gray-900">Privacidade</a>
                <a href="#" class="hover:text-gray-900">Suporte</a>
            </div>
        </div>
    </footer>

    <script>
        // Inicializar Lucide icons
        lucide.createIcons();
    </script>
</body>
</html>
