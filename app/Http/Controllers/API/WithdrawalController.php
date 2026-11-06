<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index(Request $request)
    {
        $withdrawals = $request->user()->withdrawals()->orderByDesc('created_at')->paginate(20);

        return response()->json(['success' => true, 'data' => $withdrawals]);
    }

    public function store(Request $request)
    {
        if (! $request->user()->isFreelancer()) {
            return response()->json(['success' => false, 'message' => 'Apenas freelancers'], 403);
        }

        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:10'],
            'pix_key' => ['required', 'string', 'max:100'],
        ]);

        if ((float) $validated['amount'] > $request->user()->availableBalance()) {
            return response()->json(['success' => false, 'message' => 'Saldo insuficiente'], 422);
        }

        $amount = (float) $validated['amount'];
        $fee = $amount * 0.02;

        $withdrawal = Withdrawal::create([
            'freelancer_id' => $request->user()->id,
            'amount' => $amount,
            'fee' => $fee,
            'net_amount' => $amount - $fee,
            'pix_key' => $validated['pix_key'],
            'status' => 'solicitado',
        ]);

        return response()->json(['success' => true, 'data' => $withdrawal], 201);
    }

    public function cancel(Request $request, Withdrawal $withdrawal)
    {
        if ($withdrawal->freelancer_id !== $request->user()->id) {
            return response()->json(['success' => false, 'message' => 'Saque não encontrado.'], 404);
        }

        if (! in_array($withdrawal->status, ['solicitado', 'processando'], true)) {
            return response()->json(['success' => false, 'message' => 'Apenas saques pendentes podem ser cancelados.'], 422);
        }

        $withdrawal->update(['status' => 'cancelado']);

        return response()->json(['success' => true, 'message' => 'Saque cancelado.']);
    }
}
