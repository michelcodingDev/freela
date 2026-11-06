<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardPeriodTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_dashboard_shows_period_label_and_range(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => '6m']))
            ->assertOk()
            ->assertSee('Últimos 6 meses')
            ->assertSee('Período:')
            ->assertSee('value="6m" selected', false);
    }

    public function test_invalid_period_falls_back_to_twelve_months(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => 'invalido']))
            ->assertOk()
            ->assertSee('Últimos 12 meses')
            ->assertSee('value="12m" selected', false);
    }

    public function test_new_users_card_respects_selected_period(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        // Usuário criado há 8 meses: fora do período de 1 mês, dentro de 12 meses.
        User::factory()->cliente()->create([
            'created_at' => now()->subMonths(8),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => '1m']))
            ->assertOk()
            ->assertSee('novos no período')
            ->assertSee('Último mês');

        $this->actingAs($admin)
            ->get(route('admin.dashboard', ['period' => '12m']))
            ->assertOk()
            ->assertSee('Últimos 12 meses');
    }
}
