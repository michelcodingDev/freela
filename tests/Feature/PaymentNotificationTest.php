<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Withdrawal;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class PaymentNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        config(['services.pagseguro.webhook_secret' => null]);
    }

    public function test_confirmed_payment_notifies_client_and_freelancer(): void
    {
        [$project, $client, $freelancer] = $this->projectWithAcceptedProposal();

        $payment = Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'payment_intent_id' => 'PIX_NOTIFICA',
            'status' => 'pendente',
            'amount' => 1500,
        ]);

        $this->postJson(route('payments.webhook', 'pix'), [
            'transaction_id' => 'PIX_NOTIFICA',
            'status' => 'aprovado',
        ])->assertOk();

        $this->assertDatabaseHas('notifications', [
            'user_id' => $freelancer->id,
            'type' => 'pagamento_custodia',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $client->id,
            'type' => 'pagamento_confirmado',
        ]);
        $this->assertEquals('custodia', $payment->fresh()->status);
    }

    public function test_release_notifies_freelancer_about_available_balance(): void
    {
        [$project, $client, $freelancer] = $this->projectWithAcceptedProposal(['status' => 'aguardando_liberacao']);

        $payment = Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'custodia',
            'net_amount' => 1600,
        ]);

        $this->actingAs($client)
            ->post(route('payments.release', $payment))
            ->assertRedirect(route('projects.show', $project));

        $this->assertEquals('liberado', $payment->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $freelancer->id,
            'type' => 'pagamento_liberado',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $client->id,
            'type' => 'pagamento_liberado',
        ]);
    }

    public function test_admin_paying_withdrawal_notifies_freelancer(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');
        $freelancer = User::factory()->freelancer()->create();
        $withdrawal = Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'status' => 'solicitado',
            'net_amount' => 490,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.pay', $withdrawal))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $freelancer->id,
            'type' => 'saque_pago',
        ]);
    }

    public function test_auto_release_notifies_client_and_freelancer(): void
    {
        [$project, $client, $freelancer] = $this->projectWithAcceptedProposal(['status' => 'aguardando_liberacao']);

        Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'custodia',
            'paid_at' => now()->subDays(6),
        ]);

        Artisan::call('escrow:auto-release');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $freelancer->id,
            'type' => 'liberacao_automatica',
        ]);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $client->id,
            'type' => 'liberacao_automatica',
        ]);
    }

    /**
     * @param  array<string, mixed>  $projectAttributes
     * @return array{0: Project, 1: User, 2: User}
     */
    private function projectWithAcceptedProposal(array $projectAttributes = []): array
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create(array_merge([
            'client_id' => $client->id,
            'category_id' => $category->id,
        ], $projectAttributes));

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        return [$project, $client, $freelancer];
    }
}
