<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Dispute;
use App\Models\Payment;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisputeUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function disputeContext(array $disputeOverrides = [], string $projectStatus = 'disputa'): array
    {
        $client = User::factory()->cliente()->create(['name' => 'Cliente Disputa']);
        $freelancer = User::factory()->freelancer()->create(['name' => 'Freela Disputa']);
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto em disputa',
            'status' => $projectStatus,
            'completed_at' => now(),
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $payment = Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'amount' => 1500,
            'net_amount' => 1150,
            'method' => 'pix',
            'status' => 'custodia',
        ]);

        $dispute = Dispute::factory()->create(array_merge([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'reason' => 'Entrega incompleta e fora do escopo combinado.',
            'client_argument' => 'O cliente afirma que faltam arquivos.',
            'freelancer_argument' => 'O freelancer afirma que entregou tudo.',
            'status' => 'aberta',
        ], $disputeOverrides));

        return [$client, $freelancer, $project, $payment, $dispute];
    }

    public function test_index_shows_summary_and_status_filter(): void
    {
        [$client, $freelancer, $project, $payment, $dispute] = $this->disputeContext();

        Dispute::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'reason' => 'Disputa já resolvida para teste.',
            'status' => 'resolvida',
            'winner' => 'freelancer',
        ]);

        $this->actingAs($client)
            ->get(route('disputes.index', ['status' => 'aberta']))
            ->assertOk()
            ->assertSee('Em análise')
            ->assertSee('Projeto em disputa')
            ->assertDontSee('Disputa já resolvida para teste.');
    }

    public function test_show_displays_both_arguments_and_blocked_escrow(): void
    {
        [$client, $freelancer, $project, $payment, $dispute] = $this->disputeContext();

        $this->actingAs($client)
            ->get(route('disputes.show', $dispute))
            ->assertOk()
            ->assertSee('Motivo da disputa')
            ->assertSee('Entrega incompleta e fora do escopo combinado.')
            ->assertSee('O cliente afirma que faltam arquivos.')
            ->assertSee('O freelancer afirma que entregou tudo.')
            ->assertSee('Valor em custódia')
            ->assertSee('R$ 1.500,00')
            ->assertSee('Cliente Disputa')
            ->assertSee('Freela Disputa');
    }

    public function test_resolved_dispute_shows_winner_and_partial_percentage(): void
    {
        [$client, $freelancer, $project, $payment, $dispute] = $this->disputeContext([
            'status' => 'resolvida',
            'winner' => 'parcial',
            'partial_percentage' => 60,
            'resolution_notes' => 'Divisão conforme entrega parcial.',
            'resolved_at' => now(),
        ]);

        $this->actingAs($freelancer)
            ->get(route('disputes.show', $dispute))
            ->assertOk()
            ->assertSee('Resolução')
            ->assertSee('parcial')
            ->assertSee('60,00% ao freelancer')
            ->assertSee('Divisão conforme entrega parcial.');
    }

    public function test_non_participant_cannot_view_dispute(): void
    {
        [$client, $freelancer, $project, $payment, $dispute] = $this->disputeContext();
        $outsider = User::factory()->cliente()->create();

        $this->actingAs($outsider)
            ->get(route('disputes.show', $dispute))
            ->assertForbidden();
    }

    public function test_create_form_warns_about_blocked_escrow(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto para disputar',
            'status' => 'em_andamento',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'amount' => 900,
            'method' => 'pix',
            'status' => 'custodia',
        ]);

        $this->actingAs($client)
            ->get(route('projects.disputes.create', $project))
            ->assertOk()
            ->assertSee('Abrir disputa')
            ->assertSee('Valor bloqueado')
            ->assertSee('R$ 900,00');
    }

    public function test_store_requires_a_detailed_reason(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'em_andamento',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->actingAs($client)
            ->post(route('projects.disputes.store', $project), ['reason' => 'curto'])
            ->assertSessionHasErrors('reason');

        $this->assertDatabaseMissing('disputes', ['project_id' => $project->id]);
    }
}
