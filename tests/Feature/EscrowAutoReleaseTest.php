<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class EscrowAutoReleaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_auto_releases_after_five_days(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_liberacao',
        ]);
        Proposal::factory()->accepted()->create(['project_id' => $project->id, 'freelancer_id' => $freelancer->id]);
        $payment = Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'custodia',
            'paid_at' => now()->subDays(6),
        ]);

        Artisan::call('escrow:auto-release');

        $this->assertEquals('liberado', $payment->fresh()->status);
        $this->assertEquals('concluido', $project->fresh()->status);
    }

    public function test_does_not_release_before_five_days(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_liberacao',
        ]);
        Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'custodia',
            'paid_at' => now()->subDays(2),
        ]);

        Artisan::call('escrow:auto-release');

        $this->assertEquals('custodia', Payment::first()->fresh()->status);
        $this->assertEquals('aguardando_liberacao', $project->fresh()->status);
    }
}
