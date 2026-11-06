<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function executingProject(): array
    {
        $client = User::factory()->cliente()->create(['name' => 'Cliente Entrega']);
        $freelancer = User::factory()->freelancer()->create(['name' => 'Freela Entrega']);
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto para entrega',
            'status' => 'em_andamento',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        return [$client, $freelancer, $project];
    }

    public function test_delivery_saves_note_and_timestamp_and_shows_to_client(): void
    {
        [$client, $freelancer, $project] = $this->executingProject();

        $this->actingAs($freelancer)
            ->post(route('projects.deliver', $project), [
                'delivery_note' => 'Entrega final com arquivos e instruções de acesso.',
            ])
            ->assertSessionHas('success');

        $project->refresh();
        $this->assertSame('aguardando_liberacao', $project->status);
        $this->assertSame('Entrega final com arquivos e instruções de acesso.', $project->delivery_note);
        $this->assertNotNull($project->delivered_at);

        $this->actingAs($client)
            ->get(route('projects.show', $project))
            ->assertOk()
            ->assertSee('Entrega do freelancer')
            ->assertSee('Entrega final com arquivos e instruções de acesso.');
    }

    public function test_only_hired_freelancer_can_deliver(): void
    {
        [$client, $freelancer, $project] = $this->executingProject();
        $outsider = User::factory()->freelancer()->create();

        $this->actingAs($outsider)
            ->post(route('projects.deliver', $project), ['delivery_note' => 'Nota indevida.'])
            ->assertForbidden();

        $this->assertSame('em_andamento', $project->fresh()->status);
    }

    public function test_delivery_note_is_optional(): void
    {
        [$client, $freelancer, $project] = $this->executingProject();

        $this->actingAs($freelancer)
            ->post(route('projects.deliver', $project), [])
            ->assertSessionHas('success');

        $this->assertSame('aguardando_liberacao', $project->fresh()->status);
        $this->assertNotNull($project->fresh()->delivered_at);
    }
}
