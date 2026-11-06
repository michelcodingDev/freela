<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Withdrawal;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WithdrawalUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_freelancer_can_cancel_own_pending_withdrawal(): void
    {
        $freelancer = User::factory()->freelancer()->create();

        $withdrawal = Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'amount' => 100,
            'fee' => 2,
            'net_amount' => 98,
            'status' => 'solicitado',
        ]);

        $this->actingAs($freelancer)
            ->post(route('withdrawals.cancel', $withdrawal))
            ->assertRedirect();

        $this->assertSame('cancelado', $withdrawal->fresh()->status);
    }

    public function test_freelancer_cannot_cancel_paid_withdrawal_or_others(): void
    {
        $owner = User::factory()->freelancer()->create();
        $other = User::factory()->freelancer()->create();

        $paid = Withdrawal::factory()->create([
            'freelancer_id' => $owner->id,
            'status' => 'pago',
        ]);

        $this->actingAs($owner)
            ->post(route('withdrawals.cancel', $paid))
            ->assertSessionHas('error');

        $this->assertSame('pago', $paid->fresh()->status);

        $pending = Withdrawal::factory()->create([
            'freelancer_id' => $owner->id,
            'status' => 'solicitado',
        ]);

        $this->actingAs($other)
            ->post(route('withdrawals.cancel', $pending))
            ->assertForbidden();

        $this->assertSame('solicitado', $pending->fresh()->status);
    }

    public function test_history_masks_pix_key(): void
    {
        $freelancer = User::factory()->freelancer()->create();

        Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'pix_key' => '12345678901234',
            'amount' => 100,
            'fee' => 2,
            'net_amount' => 98,
            'status' => 'pago',
        ]);

        $response = $this->actingAs($freelancer)
            ->get(route('withdrawals.index'))
            ->assertOk()
            ->assertSee('Você receberá:')
            ->assertDontSee('12345678901234');

        $this->assertStringContainsString('123', $response->getContent());
    }

    public function test_store_rejects_amount_above_available(): void
    {
        $freelancer = User::factory()->freelancer()->create();

        $this->actingAs($freelancer)
            ->post(route('withdrawals.store'), [
                'amount' => 500,
                'pix_key' => 'chave-pix-teste',
            ])
            ->assertSessionHasErrors('amount');

        $this->assertDatabaseMissing('withdrawals', [
            'freelancer_id' => $freelancer->id,
            'amount' => 500,
        ]);
    }
}
