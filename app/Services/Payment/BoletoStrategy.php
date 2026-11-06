<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;

class BoletoStrategy implements PaymentStrategyInterface
{
    public function createCheckout(array $data): PaymentResponse
    {
        try {
            Log::info('Criando pagamento Boleto', $data);

            $transactionId = 'BOLETO_'.uniqid();

            return new PaymentResponse(
                success: true,
                paymentUrl: route('payments.process', ['id' => $transactionId, 'method' => 'boleto']),
                transactionId: $transactionId,
                data: [
                    'method' => 'boleto',
                    'amount' => $data['amount'],
                    'barcode' => '00000000000000000000000000000000000000000000',
                    'digitable_line' => '00000.00000 00000.000000 00000.000000 0 00000000000000',
                    'expires_at' => now()->addDays(3)->toISOString(),
                    'pdf_url' => route('payments.boleto', ['id' => $transactionId]),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento Boleto: '.$e->getMessage());

            return new PaymentResponse(
                success: false,
                errorMessage: $e->getMessage()
            );
        }
    }

    public function handleWebhook(array $payload): WebhookResponse
    {
        try {
            Log::info('Webhook Boleto recebido', $payload);

            return new WebhookResponse(
                success: true,
                transactionId: $payload['transaction_id'] ?? '',
                status: $payload['status'] ?? 'pendente',
                data: $payload
            );
        } catch (\Exception $e) {
            Log::error('Erro no webhook Boleto: '.$e->getMessage());

            return new WebhookResponse(
                success: false,
                errorMessage: $e->getMessage()
            );
        }
    }
}
