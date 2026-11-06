<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function __construct(private WalletService $wallet) {}

    /**
     * Resumo e extrato financeiro do usuário autenticado.
     *
     * Aceita os mesmos filtros da web: direction, status e period.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Carteira não disponível para administradores.',
            ], 403);
        }

        $direction = $request->get('direction', 'all');
        $period = $request->get('period', 'all');

        $filters = [
            'direction' => in_array($direction, ['credito', 'debito', 'all'], true) ? $direction : 'all',
            'status' => $request->filled('status') ? $request->get('status') : 'all',
            'period' => in_array($period, ['all', '30d', '90d', '12m'], true) ? $period : 'all',
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $user->type,
                'summary' => $this->wallet->summary($user),
                'transactions' => $this->wallet->transactions($user, $filters)->take(50)->values(),
                'filters' => $filters,
            ],
        ]);
    }
}
