<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Withdrawal;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminWithdrawalListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function admin(): User
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_admin_filters_withdrawals_by_status_and_search(): void
    {
        $admin = $this->admin();
        $freelancer = User::factory()->freelancer()->create(['name' => 'Freela Saque']);

        Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'pix_key' => 'pix-busca-123',
            'status' => 'solicitado',
        ]);

        Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'pix_key' => 'pix-outro-999',
            'status' => 'pago',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.withdrawals.index', ['status' => 'solicitado']))
            ->assertOk()
            ->assertSee('pix-busca-123')
            ->assertDontSee('pix-outro-999')
            ->assertSee('Pendentes: 1');

        $this->actingAs($admin)
            ->get(route('admin.withdrawals.index', ['search' => 'Freela Saque']))
            ->assertOk()
            ->assertSee('pix-busca-123')
            ->assertSee('pix-outro-999');
    }

    public function test_admin_cancelling_notifies_freelancer(): void
    {
        $admin = $this->admin();
        $freelancer = User::factory()->freelancer()->create();

        $withdrawal = Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'status' => 'solicitado',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.cancel', $withdrawal))
            ->assertSessionHas('success');

        $this->assertSame('cancelado', $withdrawal->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $freelancer->id,
            'type' => 'saque_cancelado',
        ]);
    }

    public function test_non_admin_cannot_access_withdrawals(): void
    {
        User::factory()->freelancer()->create();
        $freelancer = User::factory()->freelancer()->create();

        $this->actingAs($freelancer)
            ->get(route('admin.withdrawals.index'))
            ->assertForbidden();
    }
}
