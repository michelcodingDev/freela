<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProfileSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function profilePayload(User $user, array $overrides = []): array
    {
        return array_merge([
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '11999998888',
            'city' => 'São Paulo',
            'state' => 'SP',
        ], $overrides);
    }

    public function test_changing_email_resets_verification_and_sends_notification(): void
    {
        Notification::fake();

        $user = User::factory()->cliente()->create([
            'email' => 'antigo@example.com',
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), $this->profilePayload($user, ['email' => 'novo@example.com']))
            ->assertRedirect(route('profile.show'));

        $user->refresh();
        $this->assertSame('novo@example.com', $user->email);
        $this->assertFalse($user->hasVerifiedEmail());

        Notification::assertSentTo($user, VerifyEmailNotification::class);
    }

    public function test_keeping_same_email_preserves_verification(): void
    {
        Notification::fake();

        $user = User::factory()->cliente()->create();

        $this->actingAs($user)
            ->put(route('profile.update'), $this->profilePayload($user, ['city' => 'Curitiba']))
            ->assertRedirect(route('profile.show'));

        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Notification::assertNotSentTo($user, VerifyEmailNotification::class);
    }

    public function test_profile_fields_can_be_cleared(): void
    {
        $user = User::factory()->cliente()->create();
        $user->profile()->create([
            'city' => 'São Paulo',
            'bio' => 'Bio para limpar.',
            'phone' => '11999998888',
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), $this->profilePayload($user, [
                'city' => '',
                'bio' => '',
                'phone' => '',
            ]))
            ->assertRedirect(route('profile.show'));

        $profile = $user->profile()->first();

        $this->assertNull($profile->city);
        $this->assertNull($profile->bio);
        $this->assertNull($profile->phone);
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = User::factory()->cliente()->create();

        $this->actingAs($user)
            ->put(route('profile.password.update'), [
                'current_password' => 'errada',
                'password' => 'nova-senha-123',
                'password_confirmation' => 'nova-senha-123',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertFalse(Hash::check('nova-senha-123', $user->fresh()->password));
    }

    public function test_password_can_be_changed_with_valid_current_password(): void
    {
        $user = User::factory()->cliente()->create();

        $this->actingAs($user)
            ->put(route('profile.password.update'), [
                'current_password' => 'password',
                'password' => 'nova-senha-123',
                'password_confirmation' => 'nova-senha-123',
            ])
            ->assertRedirect(route('profile.edit'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('nova-senha-123', $user->fresh()->password));
        $this->assertFalse(Hash::check('password', $user->fresh()->password));
    }
}
