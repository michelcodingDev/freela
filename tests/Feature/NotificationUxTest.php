<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Notification;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationUxTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_view_notifications(): void
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
    }

    public function test_list_shows_unread_badge_and_filter_tabs(): void
    {
        $user = User::factory()->cliente()->create();

        Notification::factory()->create([
            'user_id' => $user->id,
            'type' => 'proposta_recebida',
            'title' => 'Nova proposta recebida',
            'message' => 'Você recebeu uma proposta.',
            'is_read' => false,
        ]);

        Notification::factory()->create([
            'user_id' => $user->id,
            'type' => 'saque_pago',
            'title' => 'Saque pago',
            'message' => 'Seu saque foi pago.',
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Nova proposta recebida')
            ->assertSee('Saque pago')
            ->assertSee('Não lidas')
            ->assertSee('Marcar todas como lidas');
    }

    public function test_unread_filter_only_shows_unread_notifications(): void
    {
        $user = User::factory()->cliente()->create();

        Notification::factory()->create([
            'user_id' => $user->id,
            'title' => 'Notificação não lida',
            'is_read' => false,
        ]);

        Notification::factory()->create([
            'user_id' => $user->id,
            'title' => 'Notificação já lida',
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('notifications.index', ['status' => 'unread']))
            ->assertOk()
            ->assertSee('Notificação não lida')
            ->assertDontSee('Notificação já lida');

        $this->actingAs($user)
            ->get(route('notifications.index', ['status' => 'read']))
            ->assertOk()
            ->assertSee('Notificação já lida')
            ->assertDontSee('Notificação não lida');
    }

    public function test_clicking_notification_marks_read_and_redirects_to_project(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);

        $notification = Notification::factory()->create([
            'user_id' => $client->id,
            'type' => 'proposta_recebida',
            'title' => 'Nova proposta',
            'message' => 'Você recebeu uma proposta.',
            'data' => ['project_id' => $project->id],
            'is_read' => false,
        ]);

        $this->actingAs($client)
            ->post(route('notifications.read', $notification), ['action' => 'open'])
            ->assertRedirect(route('proposals.index', $project));

        $this->assertTrue($notification->fresh()->is_read);
        $this->assertNotNull($notification->fresh()->read_at);
    }

    public function test_marking_read_with_stay_keeps_user_on_list(): void
    {
        $user = User::factory()->cliente()->create();

        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'type' => 'saque_pago',
            'title' => 'Saque pago',
            'message' => 'Seu saque foi pago.',
            'is_read' => false,
        ]);

        $this->actingAs($user)
            ->post(route('notifications.read', $notification), ['action' => 'stay'])
            ->assertRedirect(route('notifications.index'));

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_user_cannot_mark_other_users_notification_as_read(): void
    {
        $owner = User::factory()->cliente()->create();
        $intruder = User::factory()->cliente()->create();

        $notification = Notification::factory()->create([
            'user_id' => $owner->id,
            'is_read' => false,
        ]);

        $this->actingAs($intruder)
            ->post(route('notifications.read', $notification))
            ->assertForbidden();

        $this->assertFalse($notification->fresh()->is_read);
    }

    public function test_notification_url_resolution_by_type(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);

        $cases = [
            ['type' => 'proposta_recebida', 'data' => ['project_id' => $project->id], 'expected' => route('proposals.index', $project)],
            ['type' => 'mensagem_recebida', 'data' => ['project_id' => $project->id], 'expected' => route('projects.messages.index', $project)],
            ['type' => 'convite_projeto', 'data' => [], 'expected' => route('invites.index')],
            ['type' => 'disputa_aberta', 'data' => ['project_id' => $project->id], 'expected' => route('disputes.index')],
            ['type' => 'saque_pago', 'data' => [], 'expected' => route('withdrawals.index')],
            ['type' => 'entrega_realizada', 'data' => ['project_id' => $project->id], 'expected' => route('projects.show', $project)],
        ];

        foreach ($cases as $case) {
            $notification = new Notification([
                'user_id' => $client->id,
                'type' => $case['type'],
                'data' => $case['data'],
            ]);

            $this->assertSame($case['expected'], $notification->url(), "Falha para o tipo {$case['type']}");
        }
    }
}
