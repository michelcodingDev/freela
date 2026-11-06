<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Project;
use App\Services\Payment\BoletoStrategy;
use App\Services\Payment\CreditCardStrategy;
use App\Services\Payment\PaymentService;
use App\Services\Payment\PixStrategy;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Cria um pagamento via API.
     */
    public function create(Request $request, Project $project)
    {
        if ($request->user()->id !== $project->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $proposal = $project->proposals()->where('status', 'aceita')->first();

        if (! $proposal) {
            return response()->json([
                'success' => false,
                'message' => 'Nenhuma proposta aceita encontrada',
            ], 404);
        }

        $method = $request->validated('method', 'pix');

        $strategy = match ($method) {
            'pix' => new PixStrategy,
            'cartao' => new CreditCardStrategy,
            'boleto' => new BoletoStrategy,
            default => new PixStrategy,
        };

        $this->paymentService->setStrategy($strategy);

        $result = $this->paymentService->createPayment(
            $project,
            $request->user(),
            $proposal->freelancer,
            $method
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'payment' => $result['payment'],
            'payment_url' => $result['payment_url'],
            'transaction_id' => $result['transactionId'],
            'data' => $result['data'],
        ]);
    }

    /**
     * Lista pagamentos do usuário autenticado.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $payments = Payment::where('client_id', $user->id)
            ->orWhere('freelancer_id', $user->id)
            ->with(['project', 'client', 'freelancer'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    /**
     * Libera pagamento (apenas cliente).
     */
    public function release(Request $request, Payment $payment)
    {
        if ($request->user()->id !== $payment->client_id) {
            return response()->json([
                'success' => false,
                'message' => 'Não autorizado',
            ], 403);
        }

        $result = $this->paymentService->releasePayment($payment, $request->user());

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pagamento liberado com sucesso',
            'payment' => $payment,
        ]);
    }
}
