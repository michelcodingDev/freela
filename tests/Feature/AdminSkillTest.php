<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSkillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function admin(): User
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    public function test_admin_can_update_skill(): void
    {
        $admin = $this->admin();
        $category = Category::factory()->create();
        $skill = Skill::factory()->create([
            'name' => 'Antiga',
            'category_id' => $category->id,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.skills.update', $skill), [
                'name' => 'Nova Habilidade',
                'category_id' => $category->id,
            ])
            ->assertRedirect(route('admin.skills.index'))
            ->assertSessionHas('success');

        $this->assertSame('Nova Habilidade', $skill->fresh()->name);
    }

    public function test_admin_cannot_delete_skill_in_use(): void
    {
        $admin = $this->admin();
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $skill = Skill::factory()->create([
            'name' => 'Em Uso',
            'category_id' => $category->id,
        ]);

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);
        $project->skills()->attach($skill->id);

        $this->actingAs($admin)
            ->delete(route('admin.skills.destroy', $skill))
            ->assertSessionHas('error');

        $this->assertNotNull($skill->fresh());
    }

    public function test_admin_can_search_skills(): void
    {
        $admin = $this->admin();
        $category = Category::factory()->create(['name' => 'Tecnologia']);

        Skill::factory()->create(['name' => 'Laravel', 'category_id' => $category->id]);
        Skill::factory()->create(['name' => 'Figma', 'category_id' => $category->id]);

        $this->actingAs($admin)
            ->get(route('admin.skills.index', ['search' => 'Laravel']))
            ->assertOk()
            ->assertSee('Laravel')
            ->assertDontSee('Figma');
    }
}
