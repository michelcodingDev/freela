<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function completedProject(User $client, User $freelancer, array $overrides = []): Project
    {
        $project = Project::factory()->create(array_merge([
            'client_id' => $client->id,
            'category_id' => Category::factory()->create()->id,
            'status' => 'concluido',
            'completed_at' => now(),
        ], $overrides));

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        return $project;
    }

    public function test_review_form_shows_who_is_being_reviewed_and_deadline(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create(['name' => 'Ana Dev']);
        $project = $this->completedProject($client, $freelancer);

        $this->actingAs($client)
            ->get(route('projects.reviews.create', $project))
            ->assertOk()
            ->assertSee('Você está avaliando')
            ->assertSee('Ana Dev')
            ->assertSee('Prazo de avaliação: até')
            ->assertSee('Tornar esta avaliação pública');
    }

    public function test_private_review_is_saved_and_hidden_from_public_views(): void
    {
        $client = User::factory()->cliente()->create(['name' => 'Cliente Privado']);
        $freelancer = User::factory()->freelancer()->create(['name' => 'Bruno Dev']);
        $project = $this->completedProject($client, $freelancer);

        $this->actingAs($client)
            ->post(route('projects.reviews.store', $project), [
                'rating' => 4,
                'comment' => 'Comentário privado do projeto.',
                'is_public' => '0',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('reviews', [
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'rating' => 4,
            'is_public' => false,
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertDontSee('Comentário privado do projeto.');

        $this->get(route('freelancers.show', $freelancer))
            ->assertOk()
            ->assertDontSee('Comentário privado do projeto.');
    }

    public function test_public_review_appears_on_project_page(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $project = $this->completedProject($client, $freelancer);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'rating' => 5,
            'comment' => 'Comentário público visível.',
            'is_public' => true,
        ]);

        $this->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Avaliações do projeto')
            ->assertSee('Comentário público visível.');
    }

    public function test_project_page_shows_already_reviewed_state(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $project = $this->completedProject($client, $freelancer);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'rating' => 5,
            'is_public' => true,
        ]);

        $this->actingAs($client)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Você já avaliou');
    }

    public function test_expired_review_window_is_blocked(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $project = $this->completedProject($client, $freelancer, [
            'completed_at' => now()->subDays(20),
        ]);

        $this->actingAs($client)
            ->post(route('projects.reviews.store', $project), [
                'rating' => 5,
                'comment' => 'Cheguei tarde demais.',
            ])
            ->assertSessionHas('error', 'O prazo de 14 dias para avaliação expirou.');

        $this->assertDatabaseMissing('reviews', [
            'project_id' => $project->id,
            'from_user_id' => $client->id,
        ]);
    }

    public function test_api_accepts_private_review_flag(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $client->forceFill(['api_token' => 'token-api-teste-'.str_repeat('d', 20)])->save();
        $project = $this->completedProject($client, $freelancer);

        $this->withToken($client->api_token)
            ->postJson(route('api.projects.reviews.store', $project), [
                'rating' => 3,
                'comment' => 'Avaliação privada via API.',
                'is_public' => false,
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('reviews', [
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'is_public' => false,
        ]);
    }
}
