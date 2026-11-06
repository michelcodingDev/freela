<?php

namespace App\Services\Payment;

interface PaymentStrategyInterface
{
    /**
     * Cria o checkout/pagamento no gateway.
     */
    public function createCheckout(array $data): PaymentResponse;

    /**
     * Processa webhook do gateway.
     */
    public function handleWebhook(array $payload): WebhookResponse;
}
