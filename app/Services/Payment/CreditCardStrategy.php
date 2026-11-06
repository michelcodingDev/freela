<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;

class CreditCardStrategy implements PaymentStrategyInterface
{
    public function createCheckout(array $data): PaymentResponse
    {
        try {
            Log::info('Criando pagamento Cartão de Crédito', $data);

            $transactionId = 'CARD_'.uniqid();

            return new PaymentResponse(
                success: true,
                paymentUrl: route('payments.process', ['id' => $transactionId, 'method' => 'cartao']),
                transactionId: $transactionId,
                data: [
                    'method' => 'cartao',
                    'amount' => $data['amount'],
                    'installments' => $data['installments'] ?? 1,
                    'card_last_digits' => substr($data['card_number'] ?? '', -4),
                    'status' => 'processando',
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento Cartão: '.$e->getMessage());

            return new PaymentResponse(
                success: false,
                errorMessage: $e->getMessage()
            );
        }
    }

    public function handleWebhook(array $payload): WebhookResponse
    {
        try {
            Log::info('Webhook Cartão recebido', $payload);

            return new WebhookResponse(
                success: true,
                transactionId: $payload['transaction_id'] ?? '',
                status: $payload['status'] ?? 'processando',
                data: $payload
            );
        } catch (\Exception $e) {
            Log::error('Erro no webhook Cartão: '.$e->getMessage());

            return new WebhookResponse(
                success: false,
                errorMessage: $e->getMessage()
            );
        }
    }
}
