<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ApiVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function tokenFor(User $user): string
    {
        $token = 'token-api-teste-'.str_repeat('a', 20);
        $user->forceFill(['api_token' => $token])->save();

        return $token;
    }

    public function test_api_resend_sends_notification_for_unverified_user(): void
    {
        Notification::fake();

        $user = User::factory()->cliente()->create(['email_verified_at' => null]);
        $token = $this->tokenFor($user);

        $this->withToken($token)
            ->postJson(route('api.verification.send'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('email_verified', false);

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_api_resend_reports_already_verified(): void
    {
        Notification::fake();

        $user = User::factory()->cliente()->create();
        $token = $this->tokenFor($user);

        $this->withToken($token)
            ->postJson(route('api.verification.send'))
            ->assertOk()
            ->assertJsonPath('email_verified', true);

        Notification::assertNotSentTo($user, VerifyEmailNotification::class);
    }

    public function test_api_verification_status_reflects_user(): void
    {
        $user = User::factory()->cliente()->create(['email_verified_at' => null]);
        $token = $this->tokenFor($user);

        $this->withToken($token)
            ->getJson(route('api.verification.status'))
            ->assertOk()
            ->assertJsonPath('email', $user->email)
            ->assertJsonPath('email_verified', false);
    }

    public function test_guest_cannot_use_verification_endpoints(): void
    {
        $this->postJson(route('api.verification.send'))->assertUnauthorized();
        $this->getJson(route('api.verification.status'))->assertUnauthorized();
    }
}
