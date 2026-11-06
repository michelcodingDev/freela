<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiProposalWalletTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_freelancer_lists_and_withdraws_proposal_via_api(): void
    {
        [$project] = $this->project();
        $freelancer = User::factory()->freelancer()->create(['api_token' => 'token-free']);
        $proposal = Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->withHeaders(['Authorization' => 'Bearer token-free'])
            ->getJson(route('api.proposals.mine'))
            ->assertOk()
            ->assertJsonPath('data.data.0.id', $proposal->id);

        $this->withHeaders(['Authorization' => 'Bearer token-free'])
            ->postJson(route('api.proposals.withdraw', $proposal))
            ->assertOk()
            ->assertJsonPath('data.status', 'retirada');

        $this->assertEquals('retirada', $proposal->fresh()->status);
    }

    public function test_client_accepts_pending_proposal_via_api(): void
    {
        [$project, $client] = $this->project();
        $client->forceFill(['api_token' => 'token-client'])->save();
        $freelancer = User::factory()->freelancer()->create();
        $proposal = Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->withHeaders(['Authorization' => 'Bearer token-client'])
            ->postJson(route('api.proposals.accept', [$project, $proposal]))
            ->assertOk()
            ->assertJsonPath('data.status', 'aceita');

        $this->assertEquals('aguardando_pagamento', $project->fresh()->status);
    }

    public function test_client_cannot_accept_withdrawn_proposal_via_api(): void
    {
        [$project, $client] = $this->project();
        $client->forceFill(['api_token' => 'token-client-2'])->save();
        $proposal = Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => User::factory()->freelancer()->create()->id,
            'status' => 'retirada',
        ]);

        $this->withHeaders(['Authorization' => 'Bearer token-client-2'])
            ->postJson(route('api.proposals.accept', [$project, $proposal]))
            ->assertStatus(422);
    }

    public function test_freelancer_wallet_endpoint_returns_summary_and_statement(): void
    {
        [$project, $client] = $this->project();
        $freelancer = User::factory()->freelancer()->create(['api_token' => 'token-wallet']);

        Payment::factory()->released()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'net_amount' => 850,
        ]);

        $this->withHeaders(['Authorization' => 'Bearer token-wallet'])
            ->getJson(route('api.wallet.index'))
            ->assertOk()
            ->assertJsonPath('data.summary.available', 850)
            ->assertJsonPath('data.transactions.0.status', 'liberado');
    }

    public function test_admin_cannot_access_api_wallet(): void
    {
        $admin = User::factory()->admin()->create(['api_token' => 'token-admin']);

        $this->withHeaders(['Authorization' => 'Bearer token-admin'])
            ->getJson(route('api.wallet.index'))
            ->assertForbidden();
    }

    /**
     * @return array{0: Project, 1: User}
     */
    private function project(): array
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
        ]);

        return [$project, $client];
    }
}
