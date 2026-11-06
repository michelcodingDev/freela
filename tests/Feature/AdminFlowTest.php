<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dispute;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use App\Models\Withdrawal;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_resolves_dispute_parcial(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'disputa',
        ]);
        Proposal::factory()->accepted()->create(['project_id' => $project->id, 'freelancer_id' => $freelancer->id]);
        $payment = Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'custodia',
            'net_amount' => 800,
        ]);
        $dispute = Dispute::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'aberta',
        ]);

        $this->actingAs($admin)
            ->post(route('admin.disputes.resolve', $dispute), [
                'winner' => 'parcial',
                'partial_percentage' => 50,
                'resolution_notes' => 'Divisão justa.',
            ])
            ->assertSessionHas('success');

        $this->assertEquals('resolvida', $dispute->fresh()->status);
        $this->assertEquals('concluido', $project->fresh()->status);
    }

    public function test_admin_pays_withdrawal(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');
        $freelancer = User::factory()->freelancer()->create();
        $withdrawal = Withdrawal::factory()->create(['freelancer_id' => $freelancer->id, 'status' => 'solicitado']);

        $this->actingAs($admin)
            ->post(route('admin.withdrawals.pay', $withdrawal))
            ->assertSessionHas('success');

        $this->assertEquals('pago', $withdrawal->fresh()->status);
    }
}
