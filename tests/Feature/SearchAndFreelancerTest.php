<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchAndFreelancerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_project_search_filters_by_skill_and_budget(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $skill = Skill::factory()->create(['category_id' => $category->id]);

        $match = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
            'budget_min' => 500,
            'budget_max' => 1000,
        ]);
        $match->skills()->attach($skill->id);

        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
            'budget_min' => 5000,
            'budget_max' => 9000,
        ]);

        $this->actingAs($client)
            ->get(route('projects.index', ['skill' => $skill->id]))
            ->assertOk()
            ->assertSee($match->title);

        $this->actingAs($client)
            ->get(route('projects.index', ['budget_min' => 4000]))
            ->assertOk()
            ->assertDontSee($match->title);
    }

    public function test_freelancer_vitrine_lists_and_show_public_profile(): void
    {
        $freelancer = User::factory()->freelancer()->create(['name' => 'Ana Dev']);

        $this->actingAs($freelancer)
            ->get(route('freelancers.index'))
            ->assertOk()
            ->assertSee('Ana Dev');

        $this->actingAs($freelancer)
            ->get(route('freelancers.show', $freelancer))
            ->assertOk()
            ->assertSee('Ana Dev');
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->post(route('admin.categories.store'), [
                'name' => 'Teste Categoria',
                'slug' => 'teste-categoria',
                'is_active' => true,
            ])
            ->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['slug' => 'teste-categoria']);
    }
}
