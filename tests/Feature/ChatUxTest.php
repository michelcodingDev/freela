<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Message;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChatUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function projectWithParticipants(): array
    {
        $client = User::factory()->cliente()->create(['name' => 'Cliente Chat']);
        $freelancer = User::factory()->freelancer()->create(['name' => 'Freela Chat']);
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto com chat',
            'status' => 'em_andamento',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        return [$client, $freelancer, $project];
    }

    public function test_chat_header_shows_counterpart_and_project(): void
    {
        [$client, $freelancer, $project] = $this->projectWithParticipants();

        $this->actingAs($client)
            ->get(route('projects.messages.index', $project))
            ->assertOk()
            ->assertSee('Conversando com')
            ->assertSee('Freela Chat')
            ->assertSee('Projeto com chat')
            ->assertSee('Contato externo');
    }

    public function test_opening_chat_marks_other_messages_as_read_and_shows_receipt(): void
    {
        [$client, $freelancer, $project] = $this->projectWithParticipants();

        $message = Message::factory()->create([
            'project_id' => $project->id,
            'user_id' => $client->id,
            'message' => 'Mensagem do cliente para recibo.',
            'is_read' => false,
        ]);

        // Freelancer abre o chat: marca a mensagem do cliente como lida.
        $this->actingAs($freelancer)
            ->get(route('projects.messages.index', $project))
            ->assertOk()
            ->assertSee('Mensagem do cliente para recibo.');

        $this->assertTrue($message->fresh()->is_read);

        // Cliente abre o chat: vê o recibo "Lida" nas suas mensagens.
        $this->actingAs($client)
            ->get(route('projects.messages.index', $project))
            ->assertOk()
            ->assertSee('Mensagem do cliente para recibo.')
            ->assertSee('Lida');
    }

    public function test_pagination_shows_latest_messages_first_page(): void
    {
        [$client, $freelancer, $project] = $this->projectWithParticipants();

        for ($i = 1; $i <= 51; $i++) {
            Message::factory()->create([
                'project_id' => $project->id,
                'user_id' => $client->id,
                'message' => 'Mensagem número '.$i,
                'created_at' => now()->addSeconds($i),
            ]);
        }

        $this->actingAs($client)
            ->get(route('projects.messages.index', $project))
            ->assertOk()
            ->assertSee('Mensagem número 51')
            ->assertSee('Mensagem número 2')
            ->assertDontSee('>Mensagem número 1<');
    }

    public function test_non_participant_cannot_open_chat(): void
    {
        [$client, $freelancer, $project] = $this->projectWithParticipants();
        $outsider = User::factory()->cliente()->create();

        $this->actingAs($outsider)
            ->get(route('projects.messages.index', $project))
            ->assertForbidden();
    }
}
