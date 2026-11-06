<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyProjectsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_access_my_projects(): void
    {
        $this->get(route('projects.mine'))->assertRedirect(route('login'));
    }

    public function test_client_sees_only_own_projects(): void
    {
        $client = User::factory()->cliente()->create();
        $otherClient = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Meu projeto publicado',
            'status' => 'aguardando_propostas',
        ]);

        Project::factory()->create([
            'client_id' => $otherClient->id,
            'category_id' => $category->id,
            'title' => 'Projeto de outro cliente',
        ]);

        $this->actingAs($client)
            ->get(route('projects.mine'))
            ->assertOk()
            ->assertSee('Meu projeto publicado')
            ->assertDontSee('Projeto de outro cliente')
            ->assertSee('Ver propostas');
    }

    public function test_freelancer_sees_only_hired_projects(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $otherFreelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $hired = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto em que fui contratado',
            'status' => 'em_andamento',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $hired->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $notHired = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto de outro freelancer',
            'status' => 'em_andamento',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $notHired->id,
            'freelancer_id' => $otherFreelancer->id,
        ]);

        $this->actingAs($freelancer)
            ->get(route('projects.mine'))
            ->assertOk()
            ->assertSee('Projeto em que fui contratado')
            ->assertDontSee('Projeto de outro freelancer')
            ->assertSee('Abrir chat');
    }

    public function test_status_filter_narrows_results(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto concluído',
            'status' => 'concluido',
        ]);

        Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto em andamento',
            'status' => 'em_andamento',
        ]);

        $this->actingAs($client)
            ->get(route('projects.mine', ['status' => 'concluido']))
            ->assertOk()
            ->assertSee('Projeto concluído')
            ->assertDontSee('Projeto em andamento');
    }
}
