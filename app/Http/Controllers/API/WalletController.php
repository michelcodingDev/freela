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

        return response()->json([
            'success' => true,
            'data' => [
                'role' => $user->type,
                'summary' => $this->wallet->summary($user),
                'transactions' => $this->wallet->transactions($user)->take(50)->values(),
            ],
        ]);
    }
}
