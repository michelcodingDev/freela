<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectInvite;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InviteUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function inviteContext(): array
    {
        $client = User::factory()->cliente()->create(['name' => 'Cliente Convite']);
        $freelancer = User::factory()->freelancer()->create(['name' => 'Freela Convidado']);
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto com convite',
            'status' => 'aguardando_propostas',
            'budget_min' => 600,
            'budget_max' => 900,
        ]);

        $pending = ProjectInvite::create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'convidado',
            'message' => 'Gostei do seu perfil, quer participar?',
        ]);

        return [$client, $freelancer, $project, $pending];
    }

    public function test_pending_invites_come_first_with_summary(): void
    {
        [$client, $freelancer, $project, $pending] = $this->inviteContext();

        $otherProject = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => Category::factory()->create()->id,
            'title' => 'Outro projeto',
            'status' => 'aguardando_propostas',
        ]);

        ProjectInvite::create([
            'project_id' => $otherProject->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'recusado',
        ]);

        $response = $this->actingAs($freelancer)
            ->get(route('invites.index'))
            ->assertOk()
            ->assertSee('Pendentes')
            ->assertSee('Projeto com convite')
            ->assertSee('Gostei do seu perfil')
            ->assertSee('Aceitar convite')
            ->assertSee('Recusar');

        $invites = $response->viewData('invites');

        $this->assertSame('convidado', $invites->first()->status);
    }

    public function test_status_filter_narrows_results(): void
    {
        [$client, $freelancer, $project, $pending] = $this->inviteContext();

        $this->actingAs($freelancer)
            ->get(route('invites.index', ['status' => 'aceito']))
            ->assertOk()
            ->assertDontSee('Aceitar convite')
            ->assertSee('Nenhum convite com este filtro');
    }

    public function test_accepted_invite_offers_project_and_propose_actions(): void
    {
        [$client, $freelancer, $project, $pending] = $this->inviteContext();

        $this->actingAs($freelancer)
            ->post(route('invites.accept', $pending))
            ->assertRedirect(route('projects.show', $project));

        $this->actingAs($freelancer)
            ->get(route('invites.index', ['status' => 'aceito']))
            ->assertOk()
            ->assertSee('Aceito')
            ->assertSee('Enviar proposta')
            ->assertSee('Ver projeto');
    }

    public function test_guest_cannot_view_invites(): void
    {
        $this->get(route('invites.index'))->assertRedirect(route('login'));
    }
}
