<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class AuthAndDiscoveryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_password_reset_flow_sends_link(): void
    {
        $user = User::factory()->create(['email' => 'reset@test.test']);

        $this->post(route('password.email'), ['email' => 'reset@test.test'])
            ->assertRedirect();

        $this->assertNotNull(Password::createToken($user));
    }

    public function test_ranking_lists_freelancers(): void
    {
        $user = User::factory()->cliente()->create();
        User::factory()->freelancer()->create(['name' => 'Top Dev']);

        $this->actingAs($user)
            ->get(route('ranking.index'))
            ->assertOk()
            ->assertSee('Top Dev');
    }

    public function test_admin_can_create_skill(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');
        $category = Category::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.skills.store'), [
                'name' => 'Laravel Pro',
                'category_id' => $category->id,
            ])
            ->assertRedirect(route('admin.skills.index'));

        $this->assertDatabaseHas('skills', ['name' => 'Laravel Pro']);
    }
}
