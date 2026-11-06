<?php

namespace App\Services\Payment;

class WebhookResponse
{
    public function __construct(
        public bool $success,
        public string $transactionId = '',
        public string $status = '',
        public array $data = [],
        public string $errorMessage = ''
    ) {}
}
