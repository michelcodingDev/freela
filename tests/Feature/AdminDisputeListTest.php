<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dispute;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDisputeListTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_disputes_filter_by_status_and_search(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');
        $client = User::factory()->cliente()->create(['name' => 'Cliente Busca']);
        $freelancer = User::factory()->freelancer()->create(['name' => 'Freela Busca']);
        $category = Category::factory()->create();

        $openProject = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto Aberto Busca',
            'status' => 'disputa',
        ]);

        $resolvedProject = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto Resolvido Busca',
            'status' => 'concluido',
        ]);

        Dispute::factory()->create([
            'project_id' => $openProject->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'aberta',
        ]);

        Dispute::factory()->create([
            'project_id' => $resolvedProject->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'resolvida',
            'winner' => 'cliente',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.disputes', ['status' => 'aberta']))
            ->assertOk()
            ->assertSee('Projeto Aberto Busca')
            ->assertDontSee('Projeto Resolvido Busca')
            ->assertSee('Em análise: 1');

        $this->actingAs($admin)
            ->get(route('admin.disputes', ['search' => 'Projeto Resolvido']))
            ->assertOk()
            ->assertSee('Projeto Resolvido Busca')
            ->assertDontSee('Projeto Aberto Busca');

        $this->actingAs($admin)
            ->get(route('admin.disputes', ['search' => 'Freela Busca']))
            ->assertOk()
            ->assertSee('Projeto Aberto Busca')
            ->assertSee('Projeto Resolvido Busca');
    }

    public function test_non_admin_cannot_list_disputes(): void
    {
        $client = User::factory()->cliente()->create();

        $this->actingAs($client)
            ->get(route('admin.disputes'))
            ->assertForbidden();
    }
}
