<?php
use Illuminate\Support\Str;
use App\Models\Dispute;
?>

<x-layout title="Dashboard Admin - FreelaJob">

    <!-- Chart.js para gráficos -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Dashboard Admin</h1>
                <p class="text-gray-600">Visão geral da plataforma FreelaJob</p>
            </div>
            <div class="flex gap-3">
                <select id="periodSelector" class="px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none">
                    <option value="12m" {{ $period === '12m' ? 'selected' : '' }}>Últimos 12 meses</option>
                    <option value="6m" {{ $period === '6m' ? 'selected' : '' }}>Últimos 6 meses</option>
                    <option value="3m" {{ $period === '3m' ? 'selected' : '' }}>Últimos 3 meses</option>
                    <option value="1m" {{ $period === '1m' ? 'selected' : '' }}>Último mês</option>
                </select>
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-all shadow-sm">
                    Ver Todos Usuários
                </a>
                <a href="{{ route('admin.payments.index') }}" class="px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all shadow-sm">
                    Ver Todos Pagamentos
                </a>
            </div>
        </div>

        <!-- Alertas -->
        @if ($alerts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                @foreach ($alerts as $alert)
                    <div class="bg-{{ $alert['type'] === 'error' ? 'red' : ($alert['type'] === 'warning' ? 'amber' : 'blue') }}-50 border border-{{ $alert['type'] === 'error' ? 'red' : ($alert['type'] === 'warning' ? 'amber' : 'blue') }}-200 rounded-xl p-4 flex items-start gap-3">
                        <i data-lucide="{{ $alert['icon'] }}" class="w-5 h-5 
                            {{ $alert['type'] === 'error' ? 'text-red-600' : ($alert['type'] === 'warning' ? 'text-amber-600' : 'text-blue-600') }}
                            mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">{{ $alert['title'] }}</h4>
                            <a href="{{ $alert['url'] }}" class="text-xs text-{{ $alert['type'] === 'error' ? 'red' : ($alert['type'] === 'warning' ? 'amber' : 'blue') }}-600 hover:underline mt-1 block">
                                {{ $alert['action'] }} →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Cards de Visão Geral -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Total Usuários</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($totalUsers) }}</h3>
                        <p class="text-xs text-green-600 font-medium mt-2">+{{ number_format($newUsers) }} novos (30d)</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <i data-lucide="users" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="flex gap-4 mt-4 text-xs">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                        <span class="text-gray-600">{{ $activeUsers }} Ativos</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                        <span class="text-gray-600">{{ $clientUsers }} Clientes</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span class="text-gray-600">{{ $freelancerUsers }} Freelancers</span>
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Projetos Ativos</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($activeProjects) }}</h3>
                        <p class="text-xs text-green-600 font-medium mt-2">{{ number_format($completedProjects) }} Concluídos</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                        <i data-lucide="briefcase" class="w-6 h-6"></i>
                    </div>
                </div>
                <div class="flex gap-4 mt-4 text-xs">
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <span class="text-gray-600">{{ $totalProjects }} Total</span>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                        <span class="text-gray-600">{{ $disputeProjects }} Disputas</span>
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">GMV Total</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($totalGMV, 2, ',', '.') }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Valor bruto transacionado</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600">
                        <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Receita Plataforma</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($platformRevenue, 2, ',', '.') }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Taxas de intermediação</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <i data-lucide="credit-card" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Saldo em Custódia</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($escrowBalance, 2, ',', '.') }}</h3>
                        <p class="text-xs text-amber-600 font-medium mt-1">Aguardando liberação</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                        <i data-lucide="lock" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Ticket Médio</p>
                        <h3 class="text-2xl font-bold text-gray-900 mt-1">R$ {{ number_format($avgTicket, 2, ',', '.') }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Por projeto concluído</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-green-600">
                        <i data-lucide="trending-up" class="w-6 h-6"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Gráfico 1: Receita / GMV Mensal -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">GMV vs Receita Mensal</h3>
                    <span class="text-xs text-gray-500">Últimos 12 meses</span>
                </div>
                <div class="h-64">
                    <canvas id="gmvRevenueChart"></canvas>
                </div>
            </div>

            <!-- Gráfico 2: Novos Usuários -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Novos Usuários por Tipo</h3>
                    <span class="text-xs text-gray-500">Clientes vs Freelancers</span>
                </div>
                <div class="h-64">
                    <canvas id="newUsersChart"></canvas>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

            <!-- Gráfico 3: Categorias -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Projetos por Categoria</h3>
                    <span class="text-xs text-gray-500">Distribuição</span>
                </div>
                <div class="h-64">
                    <canvas id="categoriesChart"></canvas>
                </div>
            </div>

            <!-- Gráfico 4: Métodos de Pagamento -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Pagamentos por Método</h3>
                    <span class="text-xs text-gray-500">Pix, Cartão, Boleto</span>
                </div>
                <div class="h-64">
                    <canvas id="paymentsMethodChart"></canvas>
                </div>
            </div>

            <!-- Gráfico 5: Status dos Projetos -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Projetos por Status</h3>
                    <span class="text-xs text-gray-500">Situação atual</span>
                </div>
                <div class="h-64">
                    <canvas id="projectsStatusChart"></canvas>
                </div>
            </div>

        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">

            <!-- Gráfico 6: Funil de Conversão -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Funil de Conversão</h3>
                    <span class="text-xs text-gray-500">Taxa de conversão</span>
                </div>
                <div class="h-64">
                    <canvas id="conversionFunnelChart"></canvas>
                </div>
            </div>

            <!-- Gráfico 7: Heatmap de Propostas -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-gray-900">Heatmap de Propostas</h3>
                    <span class="text-xs text-gray-500">Dia x Hora</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-center">
                        <thead>
                            <tr>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500">Dia \ Hora</th>
                                @foreach ($proposalsHeatmap['hours'] as $hour)
                                    <th class="px-3 py-2 text-xs font-semibold text-gray-500">{{ $hour }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($proposalsHeatmap['days'] as $dayIndex => $day)
                                <tr>
                                    <td class="px-3 py-2 text-xs font-medium text-gray-700">{{ $day }}</td>
                                    @foreach ($proposalsHeatmap['data'][$dayIndex] as $value)
                                        <td class="px-3 py-2"
                                            style="background-color: rgba(16, 185, 129, {{ $value / 100 }});"
                                            title="{{ $day }} {{ $hour }}: {{ $value }} propostas">
                                            <span class="text-xs font-medium {{ $value > 50 ? 'text-white' : 'text-gray-600' }}">{{ $value }}</span>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <p class="mt-3 text-xs text-gray-500 text-center">Pico: Segunda a Sexta, 14h-17h</p>
                </div>
            </div>

        </div>

        <!-- Tabelas Recentes -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Pagamentos Recentes</h3>
                        <a href="{{ route('admin.payments.index') }}" class="text-sm text-green-600 hover:text-green-700 font-medium">Ver todos →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">ID</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Cliente</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Freelancer</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Valor</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Método</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @php $recentPayments = \App\Models\Payment::with(['client', 'freelancer'])->orderBy('created_at', 'desc')->limit(5)->get(); @endphp
                                @foreach ($recentPayments as $payment)
                                    <tr>
                                        <td class="px-6 py-4 text-sm text-gray-600 font-mono">#{{ $payment->id }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $payment->client->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $payment->freelancer->name }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900">R$ {{ number_format($payment->amount, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                                @if ($payment->status === 'pendente') bg-amber-100 text-amber-700
                                                @elseif ($payment->status === 'custodia') bg-blue-100 text-blue-700
                                                @elseif ($payment->status === 'liberado') bg-green-100 text-green-700
                                                @elseif ($payment->status === 'estornado') bg-red-100 text-red-700
                                                @endif">
                                                {{ str_replace('_', ' ', $payment->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $payment->method }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-6">Resumo Rápido</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-sm text-gray-500">Taxa Média de Conversão</span>
                            <span class="text-sm font-bold text-gray-900">{{ $conversionFunnel[count($conversionFunnel)-1]['percentage'] ?? 0 }}%</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-sm text-gray-500">Projetos em Disputa</span>
                            <span class="text-sm font-bold text-red-600">{{ $disputeProjects }}</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-sm text-gray-500">Pagamentos Pendentes</span>
                            <span class="text-sm font-bold text-amber-600">
                                @php echo \App\Models\Payment::where('status', 'pendente')->count(); @endphp
                            </span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <span class="text-sm text-gray-500">Saldos em Custódia</span>
                            <span class="text-sm font-bold text-gray-900">R$ {{ number_format($escrowBalance, 2, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        // Inicializar Lucide icons
        lucide.createIcons();

        // Gráfico 1: GMV vs Receita
        new Chart(document.getElementById('gmvRevenueChart'), {
            type: 'line',
            data: {
                labels: [<?php echo implode(', ', array_map(fn($m) => "'" . date('m/Y', strtotime($m . '-01')) . "'", $months)); ?>],
                datasets: [{
                    label: 'GMV (R$)',
                    data: [<?php echo implode(', ', $monthlyGMV); ?>],
                    borderColor: '#1e3a5f',
                    backgroundColor: 'rgba(30, 58, 95, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Receita (R$)',
                    data: [<?php echo implode(', ', $monthlyRevenue); ?>],
                    borderColor: '#16a34a',
                    backgroundColor: 'rgba(22, 163, 74, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: false }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'R$' } }
                }
            }
        });

        // Gráfico 2: Novos Usuários
        new Chart(document.getElementById('newUsersChart'), {
            type: 'bar',
            data: {
                labels: [<?php echo implode(', ', array_map(fn($m) => "'" . date('m/Y', strtotime($m . '-01')) . "'", $months)); ?>],
                datasets: [{
                    label: 'Clientes',
                    data: [<?php echo implode(', ', $newUsersByMonth['clients']); ?>],
                    backgroundColor: '#3b82f6'
                }, {
                    label: 'Freelancers',
                    data: [<?php echo implode(', ', $newUsersByMonth['freelancers']); ?>],
                    backgroundColor: '#16a34a'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Usuários' } }
                }
            }
        });

        // Gráfico 3: Categorias (Pizza)
        new Chart(document.getElementById('categoriesChart'), {
            type: 'doughnut',
            data: {
                labels: [<?php echo implode(', ', array_map(fn($c) => "'" . $c['name'] . "'", $projectsByCategory)); ?>],
                datasets: [{
                    data: [<?php echo implode(', ', array_map(fn($c) => $c['value'], $projectsByCategory)); ?>],
                    backgroundColor: [<?php echo implode(', ', array_map(fn($c) => "'" . $c['color'] . "'", $projectsByCategory)); ?>]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });

        // Gráfico 4: Métodos de Pagamento (Barras)
        new Chart(document.getElementById('paymentsMethodChart'), {
            type: 'bar',
            data: {
                labels: [<?php echo implode(', ', array_map(fn($p) => "'" . ucfirst($p['method']) . "'", $paymentsByMethod)); ?>],
                datasets: [{
                    label: 'Quantidade',
                    data: [<?php echo implode(', ', array_map(fn($p) => $p['count'], $paymentsByMethod)); ?>],
                    backgroundColor: ['#16a34a', '#3b82f6', '#f59e0b', '#8b5cf6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Gráfico 5: Status dos Projetos (Barras Horizontais)
        new Chart(document.getElementById('projectsStatusChart'), {
            type: 'bar',
            data: {
                labels: [<?php echo implode(', ', array_map(fn($p) => "'" . ucfirst(str_replace('_', ' ', $p['status'])) . "'", $projectsByStatus)); ?>],
                datasets: [{
                    label: 'Quantidade',
                    data: [<?php echo implode(', ', array_map(fn($p) => $p['total'], $projectsByStatus)); ?>],
                    backgroundColor: '#1e3a5f'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true }
                }
            }
        });

        // Gráfico 6: Funil de Conversão
        new Chart(document.getElementById('conversionFunnelChart'), {
            type: 'funnel',
            data: {
                labels: [<?php echo implode(', ', array_map(fn($f) => "'" . $f['stage'] . "'", $conversionFunnel)); ?>],
                datasets: [{
                    data: [<?php echo implode(', ', array_map(fn($f) => $f['value'], $conversionFunnel)); ?>],
                    backgroundColor: ['#93c5fd', '#60a5fa', '#2563eb', '#1d4ed8', '#16a34a']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const idx = context.dataIndex;
                                return context.parsed + ' (' + <?php echo json_encode(array_column($conversionFunnel, 'percentage')); ?>[idx] + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Trocar período
        document.getElementById('periodSelector').addEventListener('change', function() {
            window.location.href = '?period=' + this.value;
        });
    </script>

</x-layout>
