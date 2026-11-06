<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_category_show_filters_by_experience_level(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create(['name' => 'Tecnologia']);

        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto iniciante',
            'status' => 'aguardando_propostas',
            'visibility' => 'publico',
            'experience_level' => 'iniciante',
        ]);

        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto especialista',
            'status' => 'aguardando_propostas',
            'visibility' => 'publico',
            'experience_level' => 'especialista',
        ]);

        $this->get(route('categories.show', ['category' => $category->id, 'experience_level' => 'iniciante']))
            ->assertOk()
            ->assertSee('Projeto iniciante')
            ->assertDontSee('Projeto especialista');
    }

    public function test_private_projects_are_hidden_from_public_category(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create(['name' => 'Design']);

        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto secreto privado',
            'status' => 'aguardando_propostas',
            'visibility' => 'privado',
        ]);

        $this->get(route('categories.show', $category))
            ->assertOk()
            ->assertDontSee('Projeto secreto privado');
    }

    public function test_inactive_category_returns_not_found(): void
    {
        $category = Category::factory()->create(['is_active' => false]);

        $this->get(route('categories.show', $category))->assertNotFound();
    }

    public function test_admin_cannot_delete_category_with_projects(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertSessionHas('error');

        $this->assertNotNull($category->fresh());
    }

    public function test_admin_can_delete_empty_category(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.categories.destroy', $category))
            ->assertRedirect(route('admin.categories.index'))
            ->assertSessionHas('success');

        $this->assertTrue($category->fresh()->trashed());
    }
}
