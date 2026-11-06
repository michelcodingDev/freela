<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebhookSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_webhook_rejects_invalid_signature(): void
    {
        config(['services.pagseguro.webhook_secret' => 'segredo']);
        $payment = $this->pendingPayment('PIX_SEGURANCA');

        $this->postJson(route('payments.webhook', 'pix'), [
            'transaction_id' => 'PIX_SEGURANCA',
            'status' => 'aprovado',
        ])->assertUnauthorized();

        $this->assertEquals('pendente', $payment->fresh()->status);
    }

    public function test_webhook_accepts_shared_secret_header(): void
    {
        config(['services.pagseguro.webhook_secret' => 'segredo']);
        $payment = $this->pendingPayment('PIX_HEADER');

        $this->withHeaders(['X-Webhook-Secret' => 'segredo'])
            ->postJson(route('payments.webhook', 'pix'), [
                'transaction_id' => 'PIX_HEADER',
                'status' => 'aprovado',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertEquals('custodia', $payment->fresh()->status);
        $this->assertNotNull($payment->fresh()->paid_at);
    }

    public function test_webhook_accepts_hmac_signature(): void
    {
        config(['services.pagseguro.webhook_secret' => 'segredo']);
        $payment = $this->pendingPayment('PIX_HMAC');

        $body = json_encode(['transaction_id' => 'PIX_HMAC', 'status' => 'pago']);
        $signature = hash_hmac('sha256', $body, 'segredo');

        $this->call(
            'POST',
            route('payments.webhook', 'pix'),
            [],
            [],
            [],
            ['HTTP_X_WEBHOOK_SIGNATURE' => $signature, 'CONTENT_TYPE' => 'application/json'],
            $body
        )->assertOk();

        $this->assertEquals('custodia', $payment->fresh()->status);
    }

    public function test_webhook_is_idempotent_for_custody_payments(): void
    {
        config(['services.pagseguro.webhook_secret' => null]);
        $payment = $this->pendingPayment('PIX_IDEMPOTENTE');

        $this->postJson(route('payments.webhook', 'pix'), [
            'transaction_id' => 'PIX_IDEMPOTENTE',
            'status' => 'aprovado',
        ])->assertOk();

        $paidAt = $payment->fresh()->paid_at;

        $this->travel(2)->minutes();

        $this->postJson(route('payments.webhook', 'pix'), [
            'transaction_id' => 'PIX_IDEMPOTENTE',
            'status' => 'aprovado',
        ])->assertOk();

        $payment->refresh();

        $this->assertEquals('custodia', $payment->status);
        $this->assertEquals($paidAt->timestamp, $payment->paid_at->timestamp);
    }

    private function pendingPayment(string $intentId): Payment
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_pagamento',
        ]);

        return Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'payment_intent_id' => $intentId,
            'status' => 'pendente',
            'paid_at' => null,
        ]);
    }
}
