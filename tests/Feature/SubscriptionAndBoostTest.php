<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Plan;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAndBoostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_only_freelancer_can_subscribe_and_duplicate_blocked(): void
    {
        $client = User::factory()->cliente()->create();
        $plan = Plan::factory()->create(['is_active' => true]);

        $this->actingAs($client)
            ->post(route('subscriptions.store', $plan))
            ->assertForbidden();

        $freelancer = User::factory()->freelancer()->create();
        Plan::factory()->create(['is_active' => false, 'slug' => 'inactive-plan']);

        $this->actingAs($freelancer)
            ->post(route('subscriptions.store', $plan))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($freelancer)
            ->post(route('subscriptions.store', $plan))
            ->assertSessionHas('info');
    }

    public function test_boost_idempotent(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
            'is_featured' => false,
        ]);

        $this->actingAs($client)
            ->post(route('projects.boost', $project))
            ->assertSessionHas('success');

        $this->actingAs($client)
            ->post(route('projects.boost', $project))
            ->assertSessionHas('info');
    }

    public function test_api_invite_accept(): void
    {
        $client = User::factory()->cliente()->create(['api_token' => 'token-invite']);
        $freelancer = User::factory()->freelancer()->create(['api_token' => 'token-free']);
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
            'visibility' => 'privado',
        ]);

        $this->withHeaders(['Authorization' => 'Bearer token-invite'])
            ->postJson(route('api.projects.invites.store', $project), ['freelancer_id' => $freelancer->id])
            ->assertCreated();

        $this->withHeaders(['Authorization' => 'Bearer token-free'])
            ->getJson(route('api.invites.index'))
            ->assertOk();
    }
}
