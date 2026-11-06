<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Project;
use App\Services\Payment\BoletoStrategy;
use App\Services\Payment\CreditCardStrategy;
use App\Services\Payment\PaymentService;
use App\Services\Payment\PixStrategy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Exibe a página de pagamento para o cliente.
     */
    public function process(Project $project, Request $request)
    {
        // Verificar se o usuário é o cliente do projeto
        if (auth()->id() !== $project->client_id) {
            abort(403, 'Você não tem permissão para pagar este projeto.');
        }

        // Verificar se o projeto está aguardando pagamento
        if ($project->status !== 'aguardando_pagamento') {
            return redirect()->route('projects.show', $project)
                ->with('info', 'Este projeto não está aguardando pagamento.');
        }

        // Buscar a proposta aceita para encontrar o freelancer
        $proposal = $project->proposals()->where('status', 'aceita')->first();

        if (! $proposal) {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Nenhuma proposta aceita encontrada.');
        }

        $freelancer = $proposal->freelancer;
        $method = $request->query('method', $request->input('method', 'pix'));

        // Definir estratégia de pagamento
        $strategy = match ($method) {
            'pix' => new PixStrategy,
            'cartao' => new CreditCardStrategy,
            'boleto' => new BoletoStrategy,
            default => new PixStrategy,
        };

        $this->paymentService->setStrategy($strategy);

        // Criar pagamento em custódia
        $result = $this->paymentService->createPayment(
            $project,
            auth()->user(),
            $freelancer,
            $method
        );

        if (! $result['success']) {
            return redirect()->route('projects.show', $project)
                ->with('error', $result['error']);
        }

        $payment = $result['payment'];

        return view('payments.process', compact('project', 'payment', 'method', 'result'));
    }

    /**
     * Exibe o boleto para download/visualização.
     */
    public function boleto(Payment $payment)
    {
        // Verificar se o usuário é o cliente
        if (auth()->id() !== $payment->client_id) {
            abort(403, 'Você não tem permissão para visualizar este boleto.');
        }

        return view('payments.boleto', compact('payment'));
    }

    /**
     * Libera o pagamento para o freelancer (apenas cliente).
     */
    public function release(Payment $payment)
    {
        // Verificar se o usuário é o cliente
        if (auth()->id() !== $payment->client_id) {
            abort(403, 'Você não tem permissão para liberar este pagamento.');
        }

        $client = Auth::user();
        $result = $this->paymentService->releasePayment($payment, $client);

        if (! $result['success']) {
            return back()->with('error', $result['error']);
        }

        return redirect()->route('projects.show', $payment->project)
            ->with('success', 'Pagamento liberado com sucesso! O freelancer receberá em breve.');
    }

    /**
     * Webhook do PagSeguro (recebe notificações de pagamento).
     */
    public function webhook(Request $request)
    {
        $method = $request->query('method', 'pix');
        $payload = $request->all();

        // Verificação opcional por segredo (se configurado)
        $secret = config('services.pagseguro.webhook_secret');
        if ($secret && $request->header('X-Webhook-Secret') !== $secret) {
            Log::warning('Webhook rejeitado por segredo inválido');

            return response()->json(['success' => false, 'message' => 'Assinatura inválida'], 401);
        }

        Log::info('Webhook recebido', [
            'method' => $method,
            'payload' => $payload,
        ]);

        $result = $this->paymentService->processWebhook($method, $payload);

        // Retornar 200 OK para o gateway independente do resultado
        return response()->json([
            'success' => $result['success'],
            'message' => $result['error'] ?? 'Webhook processado',
        ]);
    }

    /**
     * Lista todos os pagamentos (apenas admin).
     */
    public function adminIndex()
    {
        $this->authorize('ver todos pagamentos');

        $payments = Payment::with(['client', 'freelancer', 'project'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Exibe detalhes de um pagamento (admin).
     */
    public function adminShow(Payment $payment)
    {
        $this->authorize('ver todos pagamentos');

        $payment->load(['client', 'freelancer', 'project']);

        return view('admin.payments.show', compact('payment'));
    }
}
