<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_home_shows_real_platform_stats_and_recent_projects(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create(['name' => 'Freelancer Vitrine']);
        $category = Category::factory()->create(['name' => 'Desenvolvimento', 'is_active' => true]);

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto em destaque na home',
            'status' => 'aguardando_propostas',
            'visibility' => 'publico',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        Payment::factory()->released()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'net_amount' => 4321,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Projeto em destaque na home')
            ->assertSee('Desenvolvimento')
            ->assertSee('R$ 4.321')
            ->assertSee('Freelancers ativos')
            ->assertDontSee('146k+');
    }

    public function test_public_showcase_pages_are_accessible_to_guests(): void
    {
        User::factory()->freelancer()->create(['name' => 'Ana Pública']);
        Category::factory()->create(['name' => 'Design Público', 'is_active' => true]);

        $this->get(route('freelancers.index'))->assertOk()->assertSee('Ana Pública');
        $this->get(route('categories.index'))->assertOk()->assertSee('Design Público');
        $this->get(route('ranking.index'))->assertOk();
    }

    public function test_guests_can_browse_public_projects(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto público para visitantes',
            'status' => 'aguardando_propostas',
            'visibility' => 'publico',
        ]);

        $this->get(route('projects.index'))->assertOk()->assertSee('Projeto público para visitantes');
        $this->get(route('projects.show', $project))->assertOk()->assertSee('Projeto público para visitantes');
    }
}
