<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_api_lists_with_unread_count_and_filter(): void
    {
        $user = User::factory()->cliente()->create();
        $user->forceFill(['api_token' => 'token-api-teste-'.str_repeat('a', 20)])->save();

        Notification::factory()->create([
            'user_id' => $user->id,
            'title' => 'Não lida via API',
            'is_read' => false,
        ]);

        Notification::factory()->create([
            'user_id' => $user->id,
            'title' => 'Lida via API',
            'is_read' => true,
            'read_at' => now(),
        ]);

        $this->withToken($user->api_token)
            ->getJson(route('api.notifications.index'))
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('filter', 'all');

        $this->withToken($user->api_token)
            ->getJson(route('api.notifications.index', ['status' => 'unread']))
            ->assertOk()
            ->assertJsonFragment(['title' => 'Não lida via API'])
            ->assertJsonMissing(['title' => 'Lida via API']);
    }

    public function test_api_marks_single_notification_as_read_and_returns_url(): void
    {
        $user = User::factory()->cliente()->create();
        $user->forceFill(['api_token' => 'token-api-teste-'.str_repeat('b', 20)])->save();

        $notification = Notification::factory()->create([
            'user_id' => $user->id,
            'type' => 'convite_projeto',
            'title' => 'Convite',
            'is_read' => false,
        ]);

        $this->withToken($user->api_token)
            ->postJson(route('api.notifications.read', $notification))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('url', route('invites.index'));

        $this->assertTrue($notification->fresh()->is_read);
    }

    public function test_api_cannot_read_other_users_notification(): void
    {
        $owner = User::factory()->cliente()->create();
        $intruder = User::factory()->cliente()->create();
        $intruder->forceFill(['api_token' => 'token-api-teste-'.str_repeat('c', 20)])->save();

        $notification = Notification::factory()->create([
            'user_id' => $owner->id,
            'is_read' => false,
        ]);

        $this->withToken($intruder->api_token)
            ->postJson(route('api.notifications.read', $notification))
            ->assertNotFound();

        $this->assertFalse($notification->fresh()->is_read);
    }
}
