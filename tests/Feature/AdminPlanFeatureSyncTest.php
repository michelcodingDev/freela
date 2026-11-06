<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPlanFeatureSyncTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_update_syncs_plan_features(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        $plan = Plan::factory()->create(['name' => 'Plano Test']);
        PlanFeature::factory()->create([
            'plan_id' => $plan->id,
            'name' => 'Recurso antigo',
            'is_included' => true,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.plans.update', $plan), [
                'name' => 'Plano Test',
                'description' => 'Descrição atualizada.',
                'price_monthly' => 99.90,
                'commission_percentage' => 15,
                'min_commission' => 5,
                'proposals_per_month' => 50,
                'max_portfolio_items' => 12,
                'withdrawal_days' => 2,
                'features' => [
                    ['name' => 'Recurso novo', 'description' => 'Incluso', 'is_included' => '1'],
                    ['name' => 'Recurso extra', 'description' => 'Não incluso', 'is_included' => '0'],
                ],
            ])
            ->assertRedirect(route('admin.plans.index'))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('plan_features', [
            'plan_id' => $plan->id,
            'name' => 'Recurso antigo',
        ]);
        $this->assertDatabaseHas('plan_features', [
            'plan_id' => $plan->id,
            'name' => 'Recurso novo',
            'is_included' => true,
        ]);
        $this->assertDatabaseHas('plan_features', [
            'plan_id' => $plan->id,
            'name' => 'Recurso extra',
            'is_included' => false,
        ]);
        $this->assertSame(2, $plan->features()->count());
    }
}
