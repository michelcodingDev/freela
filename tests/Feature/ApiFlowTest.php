<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_api_messages_and_notifications(): void
    {
        $client = User::factory()->cliente()->create(['api_token' => 'token-sen']);
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
        ]);
        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->withHeaders(['Authorization' => 'Bearer token-sen'])
            ->postJson(route('api.projects.messages.store', $project), ['message' => 'Olá freelancer!'])
            ->assertCreated();

        $this->withHeaders(['Authorization' => 'Bearer token-sen'])
            ->getJson(route('api.notifications.index'))
            ->assertOk();
    }

    public function test_api_freelancers_index(): void
    {
        $user = User::factory()->cliente()->create(['api_token' => 'token-api']);
        User::factory()->freelancer()->create(['name' => 'Ana API']);

        $this->withHeaders(['Authorization' => 'Bearer token-api'])
            ->getJson(route('api.freelancers.index'))
            ->assertOk()
            ->assertJsonPath('data.data.0.name', 'Ana API');
    }
}
