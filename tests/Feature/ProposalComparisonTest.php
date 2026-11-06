<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalComparisonTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function projectWithProposals(): array
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto para comparar propostas',
            'status' => 'aguardando_propostas',
        ]);

        $cheap = User::factory()->freelancer()->create(['name' => 'Freela Barato']);
        $fast = User::factory()->freelancer()->create(['name' => 'Freela Rápido']);
        $rated = User::factory()->freelancer()->create(['name' => 'Freela Bem Avaliado']);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $rated->id,
            'rating' => 5,
            'is_public' => true,
        ]);

        Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $cheap->id,
            'offer' => 800,
            'estimated_days' => 10,
            'details' => 'Proposta barata e detalhada.',
            'status' => 'pendente',
        ]);

        Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $fast->id,
            'offer' => 1500,
            'estimated_days' => 2,
            'details' => 'Proposta rápida.',
            'status' => 'pendente',
        ]);

        Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $rated->id,
            'offer' => 1200,
            'estimated_days' => 7,
            'details' => 'Proposta bem avaliada.',
            'status' => 'pendente',
        ]);

        return [$client, $project];
    }

    public function test_comparison_summary_is_shown(): void
    {
        [$client, $project] = $this->projectWithProposals();

        $this->actingAs($client)
            ->get(route('proposals.index', $project))
            ->assertOk()
            ->assertSee('Total de propostas')
            ->assertSee('Menor oferta pendente')
            ->assertSee('R$ 800,00')
            ->assertSee('Menor prazo pendente')
            ->assertSee('2 dias')
            ->assertSee('Freela Barato')
            ->assertSee('Freela Rápido');
    }

    public function test_sorting_by_cheapest_and_fastest_changes_order(): void
    {
        [$client, $project] = $this->projectWithProposals();

        $cheapFirst = $this->actingAs($client)
            ->get(route('proposals.index', ['project' => $project->id, 'sort' => 'offer_asc']))
            ->assertOk()
            ->assertSee('Freela Barato');

        $this->assertLessThan(
            strpos($cheapFirst->getContent(), 'Freela Rápido'),
            strpos($cheapFirst->getContent(), 'Freela Barato')
        );

        $fastFirst = $this->actingAs($client)
            ->get(route('proposals.index', ['project' => $project->id, 'sort' => 'days_asc']))
            ->assertOk()
            ->assertSee('Freela Rápido');

        $this->assertLessThan(
            strpos($fastFirst->getContent(), 'Freela Barato'),
            strpos($fastFirst->getContent(), 'Freela Rápido')
        );
    }

    public function test_sorting_by_rating_puts_best_rated_first(): void
    {
        [$client, $project] = $this->projectWithProposals();

        $rated = User::where('name', 'Freela Bem Avaliado')->firstOrFail();

        $response = $this->actingAs($client)
            ->get(route('proposals.index', ['project' => $project->id, 'sort' => 'rating_desc']))
            ->assertOk();

        $proposals = $response->viewData('proposals');

        $this->assertSame($rated->id, $proposals->first()->freelancer_id);
    }

    public function test_status_filter_narrows_results(): void
    {
        [$client, $project] = $this->projectWithProposals();

        Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => User::factory()->freelancer()->create()->id,
            'offer' => 500,
            'estimated_days' => 5,
            'details' => 'Proposta retirada.',
            'status' => 'retirada',
        ]);

        $this->actingAs($client)
            ->get(route('proposals.index', ['project' => $project->id, 'status' => 'pendente']))
            ->assertOk()
            ->assertSee('Freela Barato')
            ->assertDontSee('Proposta retirada.');

        $this->actingAs($client)
            ->get(route('proposals.index', ['project' => $project->id, 'status' => 'retirada']))
            ->assertOk()
            ->assertSee('Proposta retirada.')
            ->assertDontSee('Freela Barato');
    }

    public function test_decision_buttons_hidden_when_project_no_longer_accepts_proposals(): void
    {
        [$client, $project] = $this->projectWithProposals();
        $project->update(['status' => 'aguardando_pagamento']);

        $this->actingAs($client)
            ->get(route('proposals.index', $project))
            ->assertOk()
            ->assertSee('não está mais recebendo propostas')
            ->assertDontSee('Aceitar proposta');
    }

    public function test_invalid_sort_and_filter_fall_back_to_defaults(): void
    {
        [$client, $project] = $this->projectWithProposals();

        $this->actingAs($client)
            ->get(route('proposals.index', ['project' => $project->id, 'sort' => 'invalido', 'status' => 'invalido']))
            ->assertOk()
            ->assertSee('Mais recentes')
            ->assertSee('Freela Barato');
    }
}
