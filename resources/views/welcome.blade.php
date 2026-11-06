<x-layout title="Bem-vindo ao FreelaJob">

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-b from-blue-900 via-blue-900 to-blue-800 py-20 md:py-32 text-white">
        <div class="absolute inset-0 opacity-20">
            <svg class="w-full h-full opacity-10">
                <defs>
                    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                        <path d="M 40 0 L 0 0 0 40" fill="none" stroke="white" stroke-width="1"/>
                    </pattern>
                </defs>
                <rect width="100%" height="100%" fill="url(#grid)"/>
            </svg>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-500/20 border border-green-500/30 text-green-300 text-sm font-medium mb-6 animate-in fade-in zoom-in duration-500">
                <span class="relative flex h-2 w-2 rounded-full animate-pulse bg-green-400"></span>
                Plataforma Online
            </div>

            <h1 class="text-5xl md:text-7xl font-bold leading-tight mb-6 animate-in fade-in slide-in-from-up duration-700">
                Conecte-se aos <br>
                <span class="gradient-text">melhores freelancers</span>
            </h1>

            <p class="text-lg md:text-xl text-blue-100 max-w-2xl mx-auto mb-10 animate-in fade-in slide-in-from-up duration-700 delay-100">
                Encontre profissionais talentosos para desenvolvimento, design, escrita, marketing e muito mais.
                Pagamento seguro com custódia via PagSeguro (Pix, Cartão, Boleto).
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center animate-in fade-in slide-in-from-up duration-700 delay-200">
                @auth
                    <a href="{{ route('projects.index') }}" class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-lg transition-all shadow-lg transform hover:scale-105">
                        Buscar Projetos
                    </a>
                    <a href="{{ route('dashboard') }}" class="px-8 py-4 bg-white hover:bg-gray-100 text-blue-900 rounded-xl font-semibold text-lg transition-all shadow-lg transform hover:scale-105">
                        Meu Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="px-8 py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-lg transition-all shadow-lg transform hover:scale-105">
                        Começar Grátis
                    </a>
                    <a href="{{ route('login') }}" class="px-8 py-4 bg-white hover:bg-gray-100 text-blue-900 rounded-xl font-semibold text-lg transition-all shadow-lg transform hover:scale-105">
                        Entrar
                    </a>
                @endauth
            </div>

            <!-- Estatísticas -->
            <div class="mt-16 flex justify-center gap-8 text-blue-200 animate-in fade-in slide-in-from-up duration-700 delay-300">
                <div class="text-center">
                    <div class="text-2xl font-bold">146k+</div>
                    <div class="text-sm opacity-80">Projetos concluídos</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">4k+</div>
                    <div class="text-sm opacity-80">Freelancers cadastrados</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">R$ 29M+</div>
                    <div class="text-sm opacity-80">Pago aos freelancers</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Como Funciona -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Como o FreelaJob funciona</h2>
                <p class="text-gray-600 max-w-xl mx-auto">Contrate e trabalhe com segurança em 4 passos simples.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ([
                    ['icon' => 'file-text', 'title' => 'Publique um projeto', 'desc' => 'Descreva o que você precisa e receba propostas de freelancers em minutos.'],
                    ['icon' => 'users', 'title' => 'Escolha o profissional', 'desc' => 'Analise perfis, portfólios e avaliações antes de contratar.'],
                    ['icon' => 'credit-card', 'title' => 'Pague com segurança', 'desc' => 'O valor fica retido em custódia até a entrega ser aprovada.'],
                    ['icon' => 'check-circle', 'title' => 'Receba o trabalho', 'desc' => 'Libera o pagamento apenas quando estiver satisfeito com o resultado.'],
                ] as $item)
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 hover:border-green-200 transition-all group">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                            <i data-lucide="{{ $item['icon'] }}" class="w-6 h-6"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-gray-600 text-sm">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

</x-layout>
