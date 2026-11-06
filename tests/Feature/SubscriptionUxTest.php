<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\PortfolioItem;
use App\Models\Proposal;
use App\Models\User;
use App\Models\UserPlanSubscription;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_plans_list_features_and_limits(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $plan = Plan::factory()->create([
            'name' => 'Pro Teste',
            'proposals_per_month' => 50,
            'max_portfolio_items' => 12,
        ]);

        PlanFeature::factory()->create([
            'plan_id' => $plan->id,
            'name' => 'Destaque no ranking',
            'is_included' => true,
        ]);

        PlanFeature::factory()->create([
            'plan_id' => $plan->id,
            'name' => 'Suporte prioritário',
            'is_included' => false,
        ]);

        $this->actingAs($freelancer)
            ->get(route('subscriptions.index'))
            ->assertOk()
            ->assertSee('Pro Teste')
            ->assertSee('Destaque no ranking')
            ->assertSee('Suporte prioritário')
            ->assertSee('50 propostas/mês')
            ->assertSee('12 itens no portfólio');
    }

    public function test_current_plan_card_shows_usage_and_days_remaining(): void
    {
        $freelancer = User::factory()->freelancer()->create();

        $plan = Plan::factory()->create([
            'name' => 'Atual Teste',
            'proposals_per_month' => 10,
            'max_portfolio_items' => 3,
        ]);

        UserPlanSubscription::factory()->create([
            'user_id' => $freelancer->id,
            'plan_id' => $plan->id,
            'status' => 'ativo',
            'starts_at' => now()->subDays(10),
            'ends_at' => now()->addDays(20),
        ]);

        Proposal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'offer' => 500,
            'estimated_days' => 5,
            'details' => 'Proposta para uso do plano.',
            'status' => 'pendente',
        ]);

        PortfolioItem::factory()->create(['freelancer_id' => $freelancer->id]);

        $this->actingAs($freelancer)
            ->get(route('subscriptions.index'))
            ->assertOk()
            ->assertSee('Seu Plano Atual')
            ->assertSee('dias restantes')
            ->assertSee('1/10')
            ->assertSee('1/3');
    }

    public function test_client_cannot_subscribe_to_a_plan(): void
    {
        $client = User::factory()->cliente()->create();
        $plan = Plan::factory()->create(['name' => 'Plano Bloqueado']);

        $this->actingAs($client)
            ->post(route('subscriptions.store', $plan))
            ->assertForbidden();

        $this->assertDatabaseMissing('user_plan_subscriptions', [
            'user_id' => $client->id,
            'plan_id' => $plan->id,
        ]);
    }
}
