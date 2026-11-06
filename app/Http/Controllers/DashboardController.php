<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Dashboard do usuário logado com estatísticas reais.
     */
    public function __invoke()
    {
        $user = Auth::user()->load('profile');

        $stats = [
            'activeProjects' => 0,
            'receivedProposals' => 0,
            'escrowTotal' => 0.0,
            'completedProjects' => 0,
            'availableBalance' => 0.0,
            'lockedBalance' => 0.0,
            'averageRating' => 0.0,
            'totalUsers' => 0,
            'gmv' => 0.0,
            'platformRevenue' => 0.0,
            'recentProjects' => collect(),
            'recentPayments' => collect(),
            'recommendedProjects' => collect(),
            'recentUsers' => collect(),
            'unreadNotifications' => 0,
        ];

        if ($user->isCliente()) {
            $myProjects = $user->projectsAsClient();

            $stats['activeProjects'] = (clone $myProjects)
                ->whereIn('status', ['aguardando_aprovacao', 'aguardando_propostas', 'aguardando_pagamento', 'em_andamento', 'aguardando_liberacao', 'disputa'])
                ->count();
            $stats['receivedProposals'] = Proposal::whereIn('project_id', $myProjects->select('id'))
                ->where('status', 'pendente')->count();
            $stats['escrowTotal'] = (float) $user->paymentsAsClient()->where('status', 'custodia')->sum('amount');
            $stats['completedProjects'] = (clone $myProjects)->where('status', 'concluido')->count();
            $stats['recentProjects'] = (clone $myProjects)
                ->with(['category'])
                ->orderByDesc('created_at')->limit(5)->get();
            $stats['recentPayments'] = $user->paymentsAsClient()
                ->with(['project'])->orderByDesc('created_at')->limit(5)->get();
        } elseif ($user->isFreelancer()) {
            $stats['availableBalance'] = $user->availableBalance();
            $stats['lockedBalance'] = $user->lockedBalance();
            $stats['averageRating'] = $user->averageRating();
            $stats['completedProjects'] = Project::where('status', 'concluido')
                ->whereHas('acceptedProposal', fn ($q) => $q->where('freelancer_id', $user->id))->count();
            $stats['activeProjects'] = Project::whereIn('status', ['aguardando_pagamento', 'em_andamento', 'aguardando_liberacao'])
                ->whereHas('acceptedProposal', fn ($q) => $q->where('freelancer_id', $user->id))->count();
            $stats['receivedProposals'] = $user->proposalsAsFreelancer()
                ->whereIn('status', ['pendente', 'aceita'])->count();
            $stats['pendingInvites'] = $user->invitesReceived()
                ->where('status', 'convidado')->count();
            $stats['recentProjects'] = Project::whereIn('status', ['aguardando_pagamento', 'em_andamento', 'aguardando_liberacao'])
                ->whereHas('acceptedProposal', fn ($q) => $q->where('freelancer_id', $user->id))
                ->with(['category'])->orderByDesc('created_at')->limit(5)->get();
            $stats['recommendedProjects'] = Project::where('status', 'aguardando_propostas')
                ->with(['category'])->orderByDesc('is_featured')->orderByDesc('created_at')->limit(5)->get();
            $stats['recentPayments'] = $user->paymentsAsFreelancer()
                ->with(['project'])->orderByDesc('created_at')->limit(5)->get();
        } elseif ($user->isAdmin()) {
            $stats['totalUsers'] = User::count();
            $stats['activeProjects'] = Project::whereIn('status', ['em_andamento', 'aguardando_liberacao'])->count();
            $stats['gmv'] = (float) Payment::where('status', 'liberado')->sum('amount');
            $stats['platformRevenue'] = (float) Payment::where('status', 'liberado')->sum('platform_fee');
            $stats['recentUsers'] = User::orderByDesc('created_at')->limit(5)->get();
            $stats['recentProjects'] = Project::orderByDesc('created_at')->limit(5)->get();
        }

        $stats['unreadNotifications'] = $user->appNotifications()->where('is_read', false)->count();

        return view('dashboard', compact('user', 'stats'));
    }
}
