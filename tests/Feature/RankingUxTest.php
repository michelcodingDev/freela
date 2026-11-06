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

class RankingUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_ranking_orders_by_public_average_then_reviews_count(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $top = User::factory()->freelancer()->create(['name' => 'Top Freelancer']);
        $mid = User::factory()->freelancer()->create(['name' => 'Mid Freelancer']);
        $unrated = User::factory()->freelancer()->create(['name' => 'Sem Avaliação']);

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'concluido',
        ]);

        foreach ([5, 5] as $rating) {
            Review::factory()->create([
                'project_id' => $project->id,
                'from_user_id' => $client->id,
                'to_user_id' => $top->id,
                'rating' => $rating,
                'is_public' => true,
            ]);
        }

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $mid->id,
            'rating' => 4,
            'is_public' => true,
        ]);

        $response = $this->get(route('ranking.index'))->assertOk();

        $freelancers = $response->viewData('freelancers');

        $this->assertSame($top->id, $freelancers[0]->id);
        $this->assertSame($mid->id, $freelancers[1]->id);
        $this->assertSame($unrated->id, $freelancers[2]->id);
    }

    public function test_private_reviews_do_not_count_in_ranking(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $privateStar = User::factory()->freelancer()->create(['name' => 'Estrela Privada']);
        $publicMid = User::factory()->freelancer()->create(['name' => 'Público Mediano']);

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'concluido',
        ]);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $privateStar->id,
            'rating' => 5,
            'is_public' => false,
        ]);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $publicMid->id,
            'rating' => 3,
            'is_public' => true,
        ]);

        $response = $this->get(route('ranking.index'))->assertOk();

        $freelancers = $response->viewData('freelancers');
        $private = $freelancers->firstWhere('id', $privateStar->id);
        $public = $freelancers->firstWhere('id', $publicMid->id);

        $this->assertEquals(0, (int) $private->reviews_received_count);
        $this->assertSame($publicMid->id, $freelancers[0]->id);
    }

    public function test_podium_and_completed_projects_are_displayed(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $freelancer = User::factory()->freelancer()->create(['name' => 'Campeão Ranking']);
        $freelancer->profile()->create(['identity_verified' => true]);

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'concluido',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'rating' => 5,
            'is_public' => true,
        ]);

        $this->get(route('ranking.index'))
            ->assertOk()
            ->assertSee('Campeão Ranking')
            ->assertSee('1º lugar')
            ->assertSee('1 projeto concluído')
            ->assertSee('Como funciona:');
    }

    public function test_inactive_freelancers_are_excluded(): void
    {
        User::factory()->freelancer()->create(['name' => 'Suspenso Oculto', 'status' => 'suspenso']);

        $this->get(route('ranking.index'))
            ->assertOk()
            ->assertDontSee('Suspenso Oculto');
    }
}
