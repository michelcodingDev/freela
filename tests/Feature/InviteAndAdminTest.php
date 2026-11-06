<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteAndAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_private_project_hidden_until_invited(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $other = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
            'visibility' => 'privado',
        ]);

        $this->actingAs($other)
            ->get(route('projects.index'))
            ->assertDontSee($project->title);

        $this->actingAs($client)
            ->post(route('projects.invites.store', $project), ['freelancer_id' => $freelancer->id])
            ->assertSessionHas('success');

        $this->actingAs($freelancer)
            ->get(route('projects.index'))
            ->assertSee($project->title);
    }

    public function test_admin_can_suspend_user(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');
        $target = User::factory()->cliente()->create();

        $this->actingAs($admin)
            ->post(route('admin.users.status', $target), ['status' => 'suspenso'])
            ->assertSessionHas('success');

        $this->assertEquals('suspenso', $target->fresh()->status);
    }
}
