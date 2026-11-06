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

class EscrowFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_freelancer_starts_and_delivers_and_client_releases(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_pagamento',
        ]);
        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'offer' => 1000,
        ]);
        $payment = Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'custodia',
            'amount' => 1000,
            'platform_fee' => 200,
            'gateway_fee' => 20,
            'net_amount' => 780,
        ]);

        $this->actingAs($freelancer)
            ->post(route('projects.start', $project))
            ->assertSessionHas('success');
        $this->assertEquals('em_andamento', $project->fresh()->status);

        $this->actingAs($freelancer)
            ->post(route('projects.deliver', $project))
            ->assertSessionHas('success');
        $this->assertEquals('aguardando_liberacao', $project->fresh()->status);

        $this->actingAs($client)
            ->post(route('payments.release', $payment))
            ->assertSessionHas('success');

        $this->assertEquals('liberado', $payment->fresh()->status);
        $this->assertEquals('concluido', $project->fresh()->status);
    }

    public function test_client_reviews_completed_project(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'concluido',
            'completed_at' => now(),
        ]);
        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($client)
            ->post(route('projects.reviews.store', $project), ['rating' => 5, 'comment' => 'Ótimo trabalho!'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'rating' => 5,
        ]);
    }

    public function test_freelancer_withdraws_available_balance(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        Payment::factory()->released()->create([
            'freelancer_id' => $freelancer->id,
            'net_amount' => 780,
        ]);

        $this->actingAs($freelancer)
            ->post(route('withdrawals.store'), ['amount' => 100, 'pix_key' => 'pix@example.com'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawals', [
            'freelancer_id' => $freelancer->id,
            'amount' => 100,
        ]);
    }

    public function test_chat_blocks_external_contact(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'em_andamento',
        ]);
        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($client)
            ->post(route('projects.messages.store', $project), ['message' => 'Meu e-mail é teste@example.com'])
            ->assertSessionHas('error');

        $this->actingAs($client)
            ->post(route('projects.messages.store', $project), ['message' => 'Olá, tudo bem? Podemos falar da entrega amanhã?'])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('messages', [
            'project_id' => $project->id,
            'user_id' => $client->id,
        ]);
    }

    public function test_dashboard_shows_real_stats(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);

        $this->actingAs($client)
            ->get(route('dashboard'))
            ->assertOk();
    }
}
