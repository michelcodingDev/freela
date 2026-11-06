<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use App\Models\UserPlanSubscription;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalCreateUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function openProject(): Project
    {
        $client = User::factory()->cliente()->create();

        return Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory()->create()->id,
            'title' => 'Projeto para proposta',
            'status' => 'aguardando_propostas',
            'budget_min' => 500,
            'budget_max' => 1000,
        ]);
    }

    public function test_create_form_shows_plan_usage_and_estimate(): void
    {
        $project = $this->openProject();
        $freelancer = User::factory()->freelancer()->create();

        $this->actingAs($freelancer)
            ->get(route('proposals.create', $project))
            ->assertOk()
            ->assertSee('Plano')
            ->assertSee('0/10')
            ->assertSee('Estimativa de recebimento')
            ->assertSee('Taxa da plataforma');
    }

    public function test_create_form_reflects_paid_plan_commission_and_usage(): void
    {
        $project = $this->openProject();
        $freelancer = User::factory()->freelancer()->create();

        $plan = Plan::factory()->create([
            'name' => 'Pro',
            'proposals_per_month' => 50,
            'commission_percentage' => 10,
            'min_commission' => 5,
        ]);

        UserPlanSubscription::factory()->create([
            'user_id' => $freelancer->id,
            'plan_id' => $plan->id,
            'status' => 'ativo',
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addMonth(),
        ]);

        Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'offer' => 1000,
            'estimated_days' => 5,
            'details' => 'Proposta já enviada neste mês para teste.',
            'status' => 'retirada',
        ]);

        $this->actingAs($freelancer)
            ->get(route('proposals.create', $project))
            ->assertOk()
            ->assertSee('Pro')
            ->assertSee('1/50')
            ->assertSee('10%');
    }

    public function test_at_limit_warning_is_shown(): void
    {
        $project = $this->openProject();
        $freelancer = User::factory()->freelancer()->create();

        $plan = Plan::factory()->create([
            'name' => 'Básico',
            'proposals_per_month' => 1,
            'commission_percentage' => 20,
            'min_commission' => 5,
        ]);

        UserPlanSubscription::factory()->create([
            'user_id' => $freelancer->id,
            'plan_id' => $plan->id,
            'status' => 'ativo',
        ]);

        Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'offer' => 700,
            'estimated_days' => 3,
            'details' => 'Proposta que atingiu o limite do mês.',
            'status' => 'retirada',
        ]);

        $this->actingAs($freelancer)
            ->get(route('proposals.create', $project))
            ->assertOk()
            ->assertSee('1/1')
            ->assertSee('limite atingido')
            ->assertSee('Ver planos');
    }
}
