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
            <div class="mt-16 grid grid-cols-2 md:grid-cols-4 gap-6 text-blue-200 animate-in fade-in slide-in-from-up duration-700 delay-300">
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ number_format($stats['open_projects'], 0, ',', '.') }}</div>
                    <div class="text-sm opacity-80">Projetos abertos</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ number_format($stats['completed_projects'], 0, ',', '.') }}</div>
                    <div class="text-sm opacity-80">Projetos concluídos</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">{{ number_format($stats['freelancers'], 0, ',', '.') }}</div>
                    <div class="text-sm opacity-80">Freelancers ativos</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold">R$ {{ number_format($stats['paid_to_freelancers'], 0, ',', '.') }}</div>
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

    <!-- Categorias -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Explore por categoria</h2>
                    <p class="text-gray-600 mt-1">Encontre o profissional certo para cada tipo de projeto.</p>
                </div>
                <a href="{{ route('categories.index') }}" class="hidden sm:inline text-sm font-semibold text-green-600 hover:text-green-700">Ver todas →</a>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach ($categories as $category)
                    <a href="{{ route('categories.show', $category) }}"
                       class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm hover:border-green-300 hover:shadow-md transition-all">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-3 text-white" style="background-color: {{ $category->color ?: '#16a34a' }}">
                            <i data-lucide="{{ $category->icon ?: 'briefcase' }}" class="w-5 h-5"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ $category->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">{{ $category->open_projects_count }} projeto(s) aberto(s)</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Projetos recentes -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Projetos recentes</h2>
                    <p class="text-gray-600 mt-1">Oportunidades publicadas por clientes agora.</p>
                </div>
                <a href="{{ route('projects.index') }}" class="hidden sm:inline text-sm font-semibold text-green-600 hover:text-green-700">Ver todos →</a>
            </div>

            @if ($projects->isEmpty())
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-10 text-center">
                    <p class="text-gray-500">Nenhum projeto aberto no momento. Seja o primeiro a publicar!</p>
                </div>
            @else
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($projects as $project)
                        <a href="{{ route('projects.show', $project) }}"
                           class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:border-green-300 hover:shadow-md transition-all flex flex-col">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-green-100 text-green-700">{{ $project->category?->name ?? 'Projeto' }}</span>
                                @if ($project->is_featured)
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">Destaque</span>
                                @endif
                            </div>
                            <h3 class="font-bold text-gray-900 mb-2">{{ $project->title }}</h3>
                            <p class="text-sm text-gray-600 line-clamp-3 flex-1">{{ $project->description }}</p>
                            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                                <span class="text-sm font-bold text-green-600">
                                    R$ {{ number_format($project->budget_min ?? 0, 0, ',', '.') }} – {{ number_format($project->budget_max ?? 0, 0, ',', '.') }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $project->proposals_count }} proposta(s)</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <!-- CTA final -->
    <section class="py-16 bg-gradient-to-r from-blue-900 to-green-700">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-white mb-4">Pronto para começar?</h2>
            <p class="text-blue-100 mb-8">Cadastre-se grátis e comece a contratar ou trabalhar com pagamento protegido.</p>
            @guest
                <a href="{{ route('register') }}" class="inline-flex px-8 py-4 bg-white text-blue-900 rounded-xl font-semibold text-lg hover:bg-gray-100 transition-all shadow-lg">
                    Criar minha conta
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="inline-flex px-8 py-4 bg-white text-blue-900 rounded-xl font-semibold text-lg hover:bg-gray-100 transition-all shadow-lg">
                    Ir para o dashboard
                </a>
            @endguest
        </div>
    </section>

</x-layout>
