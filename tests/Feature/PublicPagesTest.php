<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_institutional_pages_are_public(): void
    {
        $this->get(route('pages.terms'))->assertOk()->assertSee('Termos de Uso');
        $this->get(route('pages.privacy'))->assertOk()->assertSee('Política de Privacidade');
        $this->get(route('pages.support'))->assertOk()->assertSee('Central de Suporte');
    }

    public function test_register_links_point_to_institutional_pages(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee(route('pages.terms'), false)
            ->assertSee(route('pages.privacy'), false);
    }

    public function test_admin_sees_admin_navigation_in_layout(): void
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee(route('admin.payments'), false)
            ->assertSee(route('admin.logs.index'), false);
    }
}
