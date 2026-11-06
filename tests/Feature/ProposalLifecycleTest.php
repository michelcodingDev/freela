<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProposalLifecycleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_freelancer_can_withdraw_pending_proposal(): void
    {
        [$project, $client] = $this->project();
        $freelancer = User::factory()->freelancer()->create();
        $proposal = Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($freelancer)
            ->post(route('proposals.withdraw', $proposal))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertEquals('retirada', $proposal->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $client->id,
            'type' => 'proposta_retirada',
        ]);
    }

    public function test_freelancer_cannot_withdraw_another_users_proposal(): void
    {
        [$project] = $this->project();
        $owner = User::factory()->freelancer()->create();
        $intruder = User::factory()->freelancer()->create();
        $proposal = Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $owner->id,
        ]);

        $this->actingAs($intruder)
            ->post(route('proposals.withdraw', $proposal))
            ->assertForbidden();

        $this->assertEquals('pendente', $proposal->fresh()->status);
    }

    public function test_accepted_proposal_cannot_be_withdrawn(): void
    {
        [$project] = $this->project();
        $freelancer = User::factory()->freelancer()->create();
        $proposal = Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($freelancer)
            ->post(route('proposals.withdraw', $proposal))
            ->assertSessionHas('error');

        $this->assertEquals('aceita', $proposal->fresh()->status);
    }

    public function test_client_cannot_accept_withdrawn_proposal(): void
    {
        [$project, $client] = $this->project();
        $freelancer = User::factory()->freelancer()->create();
        $proposal = Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'retirada',
        ]);

        $this->actingAs($client)
            ->post(route('proposals.accept', [$project, $proposal]))
            ->assertSessionHas('error');

        $this->assertEquals('retirada', $proposal->fresh()->status);
        $this->assertEquals('aguardando_propostas', $project->fresh()->status);
    }

    public function test_client_cannot_accept_when_project_is_not_open(): void
    {
        [$project, $client] = $this->project(['status' => 'em_andamento']);
        $freelancer = User::factory()->freelancer()->create();
        $proposal = Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($client)
            ->post(route('proposals.accept', [$project, $proposal]))
            ->assertSessionHas('error');

        $this->assertEquals('pendente', $proposal->fresh()->status);
    }

    public function test_freelancer_can_resubmit_after_withdrawing(): void
    {
        [$project] = $this->project();
        $freelancer = User::factory()->freelancer()->create();
        Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'retirada',
        ]);

        $this->actingAs($freelancer)
            ->post(route('proposals.store', $project), [
                'offer' => 750,
                'estimated_days' => 5,
                'details' => str_repeat('Detalhes da nova proposta. ', 5),
            ])
            ->assertRedirect(route('projects.show', $project))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('proposals', [
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'pendente',
            'offer' => 750,
        ]);
    }

    public function test_mine_lists_only_own_proposals(): void
    {
        [$mine, $client] = $this->project(['title' => 'Projeto do meu perfil']);
        [$other] = $this->project(['title' => 'Projeto de outro freelancer']);
        $freelancer = User::factory()->freelancer()->create();

        Proposal::factory()->create(['project_id' => $mine->id, 'freelancer_id' => $freelancer->id]);
        Proposal::factory()->create(['project_id' => $other->id]);

        $this->actingAs($freelancer)
            ->get(route('proposals.mine'))
            ->assertOk()
            ->assertSee('Projeto do meu perfil')
            ->assertDontSee('Projeto de outro freelancer');
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array{0: Project, 1: User}
     */
    private function project(array $attributes = []): array
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create(array_merge([
            'client_id' => $client->id,
            'category_id' => $category->id,
        ], $attributes));

        return [$project, $client];
    }
}
