<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RateLimitTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_login_is_blocked_after_five_failed_attempts(): void
    {
        $payload = [
            'email' => 'intruso@example.com',
            'password' => 'senha-errada',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), $payload)
                ->assertSessionHasErrors('email');
        }

        $this->post(route('login.post'), $payload)
            ->assertSessionHasErrors('email');

        $this->assertStringContainsString(
            'Muitas tentativas',
            session('errors')->first('email')
        );
    }

    public function test_successful_login_clears_throttle_counter(): void
    {
        $user = User::factory()->cliente()->create([
            'email' => 'cliente@example.com',
            'password' => Hash::make('senha-correta'),
        ]);

        $wrong = ['email' => $user->email, 'password' => 'senha-errada'];

        for ($i = 0; $i < 4; $i++) {
            $this->post(route('login.post'), $wrong)->assertSessionHasErrors('email');
        }

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'senha-correta',
        ])->assertRedirect(route('dashboard'));

        $this->post(route('login.post'), $wrong)->assertSessionHasErrors('email');

        $this->assertAuthenticatedAs($user);
    }

    public function test_api_login_is_throttled_by_ip(): void
    {
        $payload = ['email' => 'intruso@example.com', 'password' => 'senha-errada'];

        for ($i = 0; $i < 10; $i++) {
            $this->postJson(route('api.auth.login'), $payload)->assertStatus(401);
        }

        $this->postJson(route('api.auth.login'), $payload)
            ->assertStatus(429)
            ->assertJsonStructure(['message']);
    }

    public function test_api_endpoints_are_throttled_per_user(): void
    {
        $user = User::factory()->cliente()->create();
        $user->forceFill(['api_token' => 'token-de-teste-'.str_repeat('a', 20)])->save();

        for ($i = 0; $i < 60; $i++) {
            $this->withToken($user->api_token)->getJson(route('api.me'))->assertOk();
        }

        $this->withToken($user->api_token)->getJson(route('api.me'))->assertStatus(429);
    }

    public function test_register_is_throttled_per_ip(): void
    {
        $payload = [
            'name' => '',
            'email' => 'invalido',
            'password' => '123',
            'type' => 'cliente',
        ];

        for ($i = 0; $i < 5; $i++) {
            $this->post(route('register.post'), $payload)->assertSessionHasErrors();
        }

        $this->post(route('register.post'), $payload)->assertStatus(429);
    }

    public function test_password_reset_requests_are_throttled(): void
    {
        $payload = ['email' => 'recuperar@example.com'];

        for ($i = 0; $i < 3; $i++) {
            $this->post(route('password.email'), $payload);
        }

        $this->post(route('password.email'), $payload)->assertStatus(429);
    }
}
