<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Dispute;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard principal do admin com cards e gráficos.
     */
    public function index(Request $request)
    {
        // Verificar se o usuário é admin
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $period = $request->get('period', '12m'); // 12m, 6m, 3m, 1m
        $months = $this->getMonths($period);

        // Cards de visão geral
        $totalUsers = User::count();
        $newUsers = User::where('created_at', '>=', now()->subDays(30))->count();
        $activeUsers = User::where('status', 'ativo')->count();
        $clientUsers = User::where('type', 'cliente')->count();
        $freelancerUsers = User::where('type', 'freelancer')->count();

        $totalProjects = Project::count();
        $activeProjects = Project::where('status', 'em_andamento')->count();
        $completedProjects = Project::where('status', 'concluido')->count();
        $disputeProjects = Project::where('status', 'disputa')->count();

        $totalGMV = Payment::where('status', 'liberado')->sum('amount');
        $platformRevenue = Payment::where('status', 'liberado')->sum('platform_fee');
        $escrowBalance = Payment::where('status', 'custodia')->sum('amount');
        $avgTicket = $totalProjects > 0 ? $totalGMV / $totalProjects : 0;

        // Gráfico 1: Receita / GMV mensal
        $monthlyGMV = $this->getMonthlyGMV($months);
        $monthlyRevenue = $this->getMonthlyRevenue($months);

        // Gráfico 2: Novos usuários por tipo
        $newUsersByMonth = $this->getNewUsersByMonth($months);

        // Gráfico 3: Projetos por categoria
        $projectsByCategory = $this->getProjectsByCategory();

        // Gráfico 4: Pagamentos por método
        $paymentsByMethod = $this->getPaymentsByMethod();

        // Gráfico 5: Projetos por status
        $projectsByStatus = $this->getProjectsByStatus();

        // Gráfico 6: Funil de conversão
        $conversionFunnel = $this->getConversionFunnel();

        // Gráfico 7: Heatmap de propostas (últimos 7 dias)
        $proposalsHeatmap = $this->getProposalsHeatmap();

        // Alertas
        $alerts = $this->getAlerts();

        return view('admin.dashboard.index', compact(
            'totalUsers', 'newUsers', 'activeUsers', 'clientUsers', 'freelancerUsers',
            'totalProjects', 'activeProjects', 'completedProjects', 'disputeProjects',
            'totalGMV', 'platformRevenue', 'escrowBalance', 'avgTicket',
            'monthlyGMV', 'monthlyRevenue', 'newUsersByMonth',
            'projectsByCategory', 'paymentsByMethod', 'projectsByStatus',
            'conversionFunnel', 'proposalsHeatmap', 'alerts', 'period'
        ));
    }

    /**
     * Lista todos os usuários (admin).
     */
    public function users(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $query = User::with('profile')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%'));
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Admin altera status do usuário (suspender/banir/ativar).
     */
    public function updateUserStatus(Request $request, User $user)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403);
        }

        if ($user->isAdmin()) {
            return back()->with('error', 'Não é possível alterar status de administrador.');
        }

        $validated = $request->validate([
            'status' => ['required', 'in:ativo,inativo,suspenso,banido'],
        ]);

        $user->update(['status' => $validated['status']]);

        return back()->with('success', 'Status atualizado para '.$validated['status']);
    }

    /**
     * Lista todos os pagamentos (admin), com filtros e resumo real.
     */
    public function payments(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $query = Payment::with(['client', 'freelancer', 'project'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                    ->orWhereHas('project', fn ($p) => $p->where('title', 'like', '%'.$search.'%'))
                    ->orWhereHas('client', fn ($c) => $c->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'))
                    ->orWhereHas('freelancer', fn ($f) => $f->where('name', 'like', '%'.$search.'%')->orWhere('email', 'like', '%'.$search.'%'));
            });
        }

        $payments = $query->paginate(20)->withQueryString();

        $summary = [
            'escrow' => (float) Payment::where('status', 'custodia')->sum('amount'),
            'today_count' => Payment::whereDate('created_at', today())->count(),
            'today_fees' => (float) Payment::whereDate('created_at', today())->where('status', 'liberado')->sum('platform_fee'),
            'total_gmv' => (float) Payment::where('status', 'liberado')->sum('amount'),
        ];

        return view('admin.payments.index', compact('payments', 'summary'));
    }

    /**
     * Lista todos os projetos (admin).
     */
    public function projects(Request $request)
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $query = Project::with(['client', 'category'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        $projects = $query->paginate(20)->withQueryString();

        return view('admin.projects.index', compact('projects'));
    }

    /**
     * Lista disputas (admin).
     */
    public function disputes()
    {
        if (! auth()->user()->isAdmin()) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $disputes = Dispute::with(['client', 'freelancer', 'project'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.disputes.index', compact('disputes'));
    }

    /**
     * Obtém os meses baseado no período selecionado.
     */
    protected function getMonths(string $period): array
    {
        $now = now();
        switch ($period) {
            case '6m':
                return $now->subMonths(5)->monthsUntil($now, 'Y-m');
            case '3m':
                return $now->subMonths(2)->monthsUntil($now, 'Y-m');
            case '1m':
                return [$now->format('Y-m')];
            default:
                return $now->subMonths(11)->monthsUntil($now, 'Y-m');
        }
    }

    /**
     * Obtém GMV mensal.
     */
    protected function getMonthlyGMV(array $months): array
    {
        $data = Payment::where('status', 'liberado')
            ->whereBetween('released_at', [now()->subMonths(11), now()])
            ->select(DB::raw('DATE_FORMAT(released_at, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return array_map(fn ($m) => $data[$m] ?? 0, $months);
    }

    /**
     * Obtém receita mensal.
     */
    protected function getMonthlyRevenue(array $months): array
    {
        $data = Payment::where('status', 'liberado')
            ->whereBetween('released_at', [now()->subMonths(11), now()])
            ->select(DB::raw('DATE_FORMAT(released_at, "%Y-%m") as month'), DB::raw('SUM(platform_fee) as total'))
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        return array_map(fn ($m) => $data[$m] ?? 0, $months);
    }

    /**
     * Obtém novos usuários por mês.
     */
    protected function getNewUsersByMonth(array $months): array
    {
        $data = User::whereBetween('created_at', [now()->subMonths(11), now()])
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('COUNT(*) as total'), 'type')
            ->groupBy('month', 'type')
            ->get()
            ->groupBy('month');

        $clientData = [];
        $freelancerData = [];

        foreach ($months as $m) {
            $monthData = $data[$m] ?? collect();
            $clientData[] = $monthData->where('type', 'cliente')->sum('total');
            $freelancerData[] = $monthData->where('type', 'freelancer')->sum('total');
        }

        return ['clients' => $clientData, 'freelancers' => $freelancerData];
    }

    /**
     * Obtém projetos por categoria.
     */
    protected function getProjectsByCategory(): array
    {
        return Category::withCount('projects')
            ->orderBy('projects_count', 'desc')
            ->limit(8)
            ->get()
            ->map(function ($cat) {
                return [
                    'name' => $cat->name,
                    'value' => $cat->projects_count,
                    'color' => $cat->color ?? '#64748b',
                ];
            })
            ->toArray();
    }

    /**
     * Obtém pagamentos por método.
     */
    protected function getPaymentsByMethod(): array
    {
        return Payment::select('method', DB::raw('COUNT(*) as total'), DB::raw('SUM(amount) as amount'))
            ->groupBy('method')
            ->get()
            ->map(function ($item) {
                return [
                    'method' => $item->method,
                    'count' => $item->total,
                    'amount' => $item->amount,
                ];
            })
            ->toArray();
    }

    /**
     * Obtém projetos por status.
     */
    protected function getProjectsByStatus(): array
    {
        return Project::select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->get()
            ->map(function ($item) {
                return [
                    'status' => str_replace('_', ' ', $item->status),
                    'total' => $item->total,
                ];
            })
            ->toArray();
    }

    /**
     * Obtém funil de conversão.
     */
    protected function getConversionFunnel(): array
    {
        $published = Project::count();
        $withProposals = Project::whereHas('proposals')->count();
        $hired = Project::whereIn('status', ['aguardando_pagamento', 'em_andamento', 'aguardando_liberacao', 'concluido'])->count();
        $paid = Payment::where('status', 'custodia')->count();
        $completed = Project::where('status', 'concluido')->count();

        return [
            ['stage' => 'Publicados', 'value' => $published, 'percentage' => 100],
            ['stage' => 'Com Propostas', 'value' => $withProposals, 'percentage' => $published > 0 ? round(($withProposals / $published) * 100, 1) : 0],
            ['stage' => 'Contratados', 'value' => $hired, 'percentage' => $published > 0 ? round(($hired / $published) * 100, 1) : 0],
            ['stage' => 'Pagos', 'value' => $paid, 'percentage' => $published > 0 ? round(($paid / $published) * 100, 1) : 0],
            ['stage' => 'Concluídos', 'value' => $completed, 'percentage' => $published > 0 ? round(($completed / $published) * 100, 1) : 0],
        ];
    }

    /**
     * Obtém heatmap de propostas (últimos 7 dias, dados reais).
     */
    protected function getProposalsHeatmap(): array
    {
        $days = ['Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sab', 'Dom'];
        $hours = ['00h', '03h', '06h', '09h', '12h', '15h', '18h', '21h'];
        $hourBuckets = [0, 3, 6, 9, 12, 15, 18, 21];

        // DAYOFWEEK: 1=Dom..7=Sab → Seg=2..Dom=1
        $rows = Proposal::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DAYOFWEEK(created_at) as dow, HOUR(created_at) as hour_bucket_floor, COUNT(*) as total')
            ->groupBy('dow', 'hour_bucket_floor')
            ->get();

        $data = array_fill(0, 7, array_fill(0, count($hours), 0));

        foreach ($rows as $row) {
            $dow = (int) $row->dow; // 1 Dom
            $dayIndex = [1 => 6, 2 => 0, 3 => 1, 4 => 2, 5 => 3, 6 => 4, 7 => 5][$dow] ?? 0;
            $hour = (int) $row->hour_bucket_floor;
            $bucket = 0;
            foreach ($hourBuckets as $i => $hb) {
                if ($hour >= $hb) {
                    $bucket = $i;
                }
            }
            $data[$dayIndex][$bucket] += (int) $row->total;
        }

        return [
            'days' => $days,
            'hours' => $hours,
            'data' => $data,
        ];
    }

    /**
     * Obtém alertas do sistema.
     */
    protected function getAlerts(): array
    {
        $alerts = [];

        // Pagamentos em custódia há mais de 5 dias
        $oldEscrow = Payment::where('status', 'custodia')
            ->where('created_at', '<', now()->subDays(5))
            ->count();

        if ($oldEscrow > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'clock',
                'title' => "$oldEscrow pagamento(s) em custódia há mais de 5 dias",
                'action' => 'Verificar liberações pendentes',
                'url' => route('admin.payments'),
            ];
        }

        // Projetos em disputa
        $disputes = Dispute::where('status', 'aberta')->count();
        if ($disputes > 0) {
            $alerts[] = [
                'type' => 'error',
                'icon' => 'alert-circle',
                'title' => "$disputes disputa(s) aberta(s) aguardando resolução",
                'action' => 'Gerenciar disputas',
                'url' => route('admin.disputes'),
            ];
        }

        // Usuários suspensos
        $suspended = User::where('status', 'suspenso')->count();
        if ($suspended > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'user-x',
                'title' => "$suspended usuário(s) suspenso(s)",
                'action' => 'Revisar usuários',
                'url' => route('admin.users'),
            ];
        }

        return $alerts;
    }
}
