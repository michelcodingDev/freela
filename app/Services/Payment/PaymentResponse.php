<?php

namespace App\Services\Payment;

class PaymentResponse
{
    public function __construct(
        public bool $success,
        public string $paymentUrl = '',
        public string $transactionId = '',
        public array $data = [],
        public string $errorMessage = ''
    ) {}
}
