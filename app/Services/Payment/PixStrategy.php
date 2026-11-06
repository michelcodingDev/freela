<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Log;

class PixStrategy implements PaymentStrategyInterface
{
    public function createCheckout(array $data): PaymentResponse
    {
        try {
            // Aqui seria a integração real com o SDK do PagSeguro
            // Para MVP, simulamos a criação de pagamento Pix
            Log::info('Criando pagamento Pix', $data);

            $transactionId = 'PIX_'.uniqid();

            // Em produção, usar o SDK do PagSeguro:
            // $payment = new \PagSeguro\Domains\Requests\DirectPayment\Request();
            // ... configuração do Pix

            return new PaymentResponse(
                success: true,
                paymentUrl: route('payments.process', ['project' => $data['project_id'], 'method' => 'pix']),
                transactionId: $transactionId,
                data: [
                    'method' => 'pix',
                    'amount' => $data['amount'],
                    'qr_code' => '00020126580014br.gov.bcb.pix0136'.uniqid().'@freelajob.com.br',
                    'qr_code_image' => '',
                    'expires_at' => now()->addMinutes(30)->toISOString(),
                ]
            );
        } catch (\Exception $e) {
            Log::error('Erro ao criar pagamento Pix: '.$e->getMessage());

            return new PaymentResponse(
                success: false,
                errorMessage: $e->getMessage()
            );
        }
    }

    public function handleWebhook(array $payload): WebhookResponse
    {
        try {
            Log::info('Webhook Pix recebido', $payload);

            return new WebhookResponse(
                success: true,
                transactionId: $payload['transaction_id'] ?? '',
                status: $payload['status'] ?? 'processando',
                data: $payload
            );
        } catch (\Exception $e) {
            Log::error('Erro no webhook Pix: '.$e->getMessage());

            return new WebhookResponse(
                success: false,
                errorMessage: $e->getMessage()
            );
        }
    }
}
