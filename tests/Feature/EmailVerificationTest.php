<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_registration_sends_verification_notification(): void
    {
        Notification::fake();

        $this->post(route('register.post'), [
            'name' => 'Novo Cliente',
            'email' => 'novo@example.com',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'senha-forte-123',
            'type' => 'cliente',
            'terms' => '1',
        ])->assertRedirect(route('dashboard'));

        $user = User::where('email', 'novo@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmailNotification::class);
        $this->assertFalse($user->hasVerifiedEmail());
        $this->assertAuthenticatedAs($user);
    }

    public function test_api_registration_sends_verification_notification(): void
    {
        Notification::fake();

        $this->postJson(route('api.auth.register'), [
            'name' => 'Api Cliente',
            'email' => 'api@example.com',
            'password' => 'senha-forte-123',
            'password_confirmation' => 'senha-forte-123',
            'type' => 'cliente',
            'terms' => true,
        ])->assertCreated()->assertJsonPath('success', true);

        $user = User::where('email', 'api@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmailNotification::class);
        $this->assertFalse($user->hasVerifiedEmail());
    }

    public function test_unverified_user_can_still_use_dashboard_but_sees_banner(): void
    {
        $user = User::factory()->cliente()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Confirme seu e-mail', false)
            ->assertSee(route('verification.notice'), false);
    }

    public function test_guest_cannot_access_verification_pages(): void
    {
        $this->get(route('verification.notice'))->assertRedirect(route('login'));
        $this->post(route('verification.send'))->assertRedirect(route('login'));
    }

    public function test_verification_notice_shows_resend_form_for_unverified_user(): void
    {
        $user = User::factory()->cliente()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertOk()
            ->assertSee('Confirme seu e-mail')
            ->assertSee($user->email)
            ->assertSee(route('verification.send'), false);
    }

    public function test_resend_sends_a_new_notification(): void
    {
        Notification::fake();

        $user = User::factory()->cliente()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertSessionHas('success');

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_signed_link_marks_email_as_verified(): void
    {
        $user = User::factory()->cliente()->create(['email_verified_at' => null]);

        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $this->actingAs($user)
            ->get($url)
            ->assertRedirect(route('dashboard'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_verified_user_is_redirected_away_from_notice_and_resend(): void
    {
        Notification::fake();

        $user = User::factory()->cliente()->create();

        $this->actingAs($user)
            ->get(route('verification.notice'))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($user)
            ->post(route('verification.send'))
            ->assertRedirect(route('dashboard'));

        Notification::assertNotSentTo($user, VerifyEmailNotification::class);
    }

    public function test_unsigned_verification_link_is_rejected(): void
    {
        $user = User::factory()->cliente()->create(['email_verified_at' => null]);

        $this->actingAs($user)
            ->get(route('verification.verify', ['id' => $user->id, 'hash' => sha1($user->email)]))
            ->assertForbidden();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
