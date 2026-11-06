<?php

namespace App\Services\Payment;

use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    /**
     * @var PaymentStrategyInterface
     */
    protected $strategy;

    /**
     * Define a estratégia de pagamento.
     */
    public function setStrategy(PaymentStrategyInterface $strategy): self
    {
        $this->strategy = $strategy;

        return $this;
    }

    /**
     * Cria um pagamento em custódia (escrow).
     */
    public function createPayment(
        Project $project,
        User $client,
        User $freelancer,
        string $method,
        array $methodData = []
    ): array {
        // Idempotência: reutiliza pagamento ativo para não duplicar ao recarregar a página.
        $existing = $project->payments()
            ->whereIn('status', ['pendente', 'custodia'])
            ->latest('created_at')
            ->first();

        if ($existing) {
            return [
                'success' => true,
                'payment' => $existing,
                'payment_url' => $existing->gateway_response['payment_url'] ?? null,
                'transaction_id' => $existing->payment_intent_id,
                'data' => $existing->gateway_response ?? [],
            ];
        }

        // Valor do projeto = oferta aceita (mais taxas) ou orçamento
        $acceptedProposal = $project->acceptedProposal;
        if ($acceptedProposal) {
            $amount = (float) $acceptedProposal->offer;
        } else {
            $amount = $project->budget_max ?? $project->budget_min ?? 0;
        }

        if ($amount <= 0) {
            return [
                'success' => false,
                'error' => 'Valor do projeto inválido para pagamento.',
            ];
        }

        // Calcular taxas
        $platformFee = $this->calculatePlatformFee($amount, $freelancer);
        $gatewayFee = $this->calculateGatewayFee($amount, $method);
        $netAmount = $amount - $platformFee - $gatewayFee;

        // Criar pagamento via estratégia
        $response = $this->strategy->createCheckout([
            'amount' => $amount,
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            ...$methodData,
        ]);

        if (! $response->success) {
            Log::error('Falha ao criar pagamento: '.$response->errorMessage);

            return [
                'success' => false,
                'error' => $response->errorMessage,
            ];
        }

        // Salvar pagamento no banco (status: pendente)
        $payment = Payment::create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'amount' => $amount,
            'platform_fee' => $platformFee,
            'gateway_fee' => $gatewayFee,
            'net_amount' => $netAmount,
            'method' => $method,
            'status' => 'pendente',
            'payment_intent_id' => $response->transactionId,
            'gateway_response' => $response->data,
        ]);

        // Atualizar projeto para aguardando pagamento
        $project->update(['status' => 'aguardando_pagamento']);

        Log::info('Pagamento criado em custódia', [
            'payment_id' => $payment->id,
            'amount' => $amount,
            'method' => $method,
        ]);

        return [
            'success' => true,
            'payment' => $payment,
            'payment_url' => $response->paymentUrl,
            'transaction_id' => $response->transactionId,
            'data' => $response->data,
        ];
    }

    /**
     * Processa webhook do gateway de pagamento.
     */
    public function processWebhook(string $method, array $payload): array
    {
        $strategy = $this->getStrategyByMethod($method);
        $this->setStrategy($strategy);

        $response = $this->strategy->handleWebhook($payload);

        if (! $response->success) {
            Log::error('Falha ao processar webhook: '.$response->errorMessage);

            return [
                'success' => false,
                'error' => $response->errorMessage,
            ];
        }

        // Buscar pagamento pelo transaction_id
        $payment = Payment::where('payment_intent_id', $response->transactionId)->first();

        if (! $payment) {
            Log::warning('Pagamento não encontrado para webhook', [
                'transaction_id' => $response->transactionId,
            ]);

            return [
                'success' => false,
                'error' => 'Pagamento não encontrado',
            ];
        }

        // Idempotência do webhook: se já está em custódia, não reaplica a transição.
        if ($payment->status === 'custodia' && in_array($response->status, ['aprovado', 'concluido', 'pago'], true)) {
            return ['success' => true, 'payment' => $payment];
        }

        if ($payment->status === 'estornado' && in_array($response->status, ['cancelado', 'estornado'], true)) {
            return ['success' => true, 'payment' => $payment];
        }

        // Atualizar status do pagamento
        $statusMap = [
            'aprovado' => 'custodia',
            'concluido' => 'custodia',
            'pago' => 'custodia',
            'processando' => 'pendente',
            'pendente' => 'pendente',
            'cancelado' => 'estornado',
            'estornado' => 'estornado',
        ];

        $newStatus = $statusMap[$response->status] ?? $response->status;

        $payment->update([
            'status' => $newStatus,
            'gateway_response' => array_merge($payment->gateway_response ?? [], $response->data),
            'paid_at' => in_array($newStatus, ['custodia']) ? now() : $payment->paid_at,
        ]);

        Log::info('Pagamento atualizado via webhook', [
            'payment_id' => $payment->id,
            'new_status' => $newStatus,
        ]);

        return [
            'success' => true,
            'payment' => $payment,
        ];
    }

    /**
     * Libera o pagamento para o freelancer (apenas o cliente pode fazer).
     */
    public function releasePayment(Payment $payment, User $client): array
    {
        // Verificar se o usuário é o cliente do pagamento
        if ($payment->client_id !== $client->id) {
            return [
                'success' => false,
                'error' => 'Você não tem permissão para liberar este pagamento.',
            ];
        }

        // Verificar se o pagamento está em custódia
        if ($payment->status !== 'custodia') {
            return [
                'success' => false,
                'error' => 'Este pagamento não está em custódia.',
            ];
        }

        // Liberar pagamento
        $payment->update([
            'status' => 'liberado',
            'released_at' => now(),
        ]);

        // Atualizar projeto para concluído
        $project = $payment->project;
        $project->update([
            'status' => 'concluido',
            'completed_at' => now(),
        ]);

        Log::info('Pagamento liberado para freelancer', [
            'payment_id' => $payment->id,
            'freelancer_id' => $payment->freelancer_id,
            'net_amount' => $payment->net_amount,
        ]);

        return [
            'success' => true,
            'payment' => $payment,
        ];
    }

    /**
     * Calcula a taxa da plataforma com base no plano do freelancer.
     */
    protected function calculatePlatformFee(float $amount, User $freelancer): float
    {
        $subscription = $freelancer->subscriptions()->where('status', 'ativo')
            ->where('ends_at', '>', now())
            ->first();

        $percentage = $subscription?->plan->commission_percentage ?? 20.00;
        $minFee = $subscription?->plan->min_commission ?? 5.00;

        $fee = ($amount * $percentage) / 100;

        return max($fee, $minFee);
    }

    /**
     * Calcula a taxa do gateway com base no método de pagamento.
     */
    protected function calculateGatewayFee(float $amount, string $method): float
    {
        $fees = [
            'pix' => 0.02,       // 2%
            'cartao' => 0.0499,  // 4.99%
            'boleto' => 0.0179,  // 1.79%
            'paypal' => 0.0640,  // 6.40%
        ];

        $percentage = $fees[$method] ?? 0.02;

        return $amount * $percentage;
    }

    /**
     * Retorna a estratégia baseada no método de pagamento.
     */
    protected function getStrategyByMethod(string $method): PaymentStrategyInterface
    {
        return match ($method) {
            'pix' => new PixStrategy,
            'cartao', 'credit_card' => new CreditCardStrategy,
            'boleto' => new BoletoStrategy,
            default => new PixStrategy,
        };
    }
}
