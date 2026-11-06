<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessRuleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_proposal_monthly_limit_blocks_eleventh_proposal(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        for ($i = 1; $i <= 10; $i++) {
            $project = Project::factory()->create([
                'client_id' => $client->id,
                'category_id' => $category->id,
                'status' => 'aguardando_propostas',
            ]);
            Proposal::factory()->create([
                'project_id' => $project->id,
                'freelancer_id' => $freelancer->id,
            ]);
        }

        $extra = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);

        $this->actingAs($freelancer)
            ->post(route('proposals.store', $extra), [
                'offer' => 500,
                'estimated_days' => 7,
                'details' => str_repeat('detalhe válido para proposta. ', 5),
            ])
            ->assertSessionHas('error');
    }

    public function test_payment_idempotency_reuses_existing_custody(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_pagamento',
        ]);
        $proposal = Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'offer' => 1000,
        ]);

        $this->actingAs($client)
            ->get(route('payments.process', $project))
            ->assertOk();

        $this->actingAs($client)
            ->get(route('payments.process', $project))
            ->assertOk();

        $this->assertEquals(1, $project->payments()->count());
    }
}
