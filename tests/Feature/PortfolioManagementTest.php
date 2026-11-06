<?php

namespace Tests\Feature;

use App\Models\PortfolioItem;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortfolioManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_index_shows_plan_usage_and_visibility_badges(): void
    {
        $freelancer = User::factory()->freelancer()->create();

        PortfolioItem::factory()->create([
            'freelancer_id' => $freelancer->id,
            'title' => 'Item público',
            'is_public' => true,
        ]);

        PortfolioItem::factory()->create([
            'freelancer_id' => $freelancer->id,
            'title' => 'Item privado',
            'is_public' => false,
        ]);

        $this->actingAs($freelancer)
            ->get(route('portfolio.index'))
            ->assertOk()
            ->assertSee('2/3')
            ->assertSee('Item público')
            ->assertSee('Item privado')
            ->assertSee('Público')
            ->assertSee('Privado');
    }

    public function test_owner_can_update_item_and_make_it_public(): void
    {
        $freelancer = User::factory()->freelancer()->create();

        $item = PortfolioItem::factory()->create([
            'freelancer_id' => $freelancer->id,
            'title' => 'Título antigo',
            'description' => 'Descrição antiga.',
            'is_public' => false,
        ]);

        $this->actingAs($freelancer)
            ->put(route('portfolio.update', $item), [
                'title' => 'Título novo',
                'description' => 'Descrição nova.',
                'external_url' => 'https://exemplo.com/projeto',
                'is_public' => '1',
            ])
            ->assertRedirect(route('portfolio.index'))
            ->assertSessionHas('success');

        $item->refresh();

        $this->assertSame('Título novo', $item->title);
        $this->assertSame('Descrição nova.', $item->description);
        $this->assertTrue((bool) $item->is_public);
    }

    public function test_toggle_visibility_flips_public_flag(): void
    {
        $freelancer = User::factory()->freelancer()->create();

        $item = PortfolioItem::factory()->create([
            'freelancer_id' => $freelancer->id,
            'is_public' => true,
        ]);

        $this->actingAs($freelancer)
            ->post(route('portfolio.toggle', $item))
            ->assertRedirect();

        $this->assertFalse((bool) $item->fresh()->is_public);

        $this->actingAs($freelancer)
            ->post(route('portfolio.toggle', $item))
            ->assertRedirect();

        $this->assertTrue((bool) $item->fresh()->is_public);
    }

    public function test_cannot_update_or_toggle_other_freelancers_item(): void
    {
        $owner = User::factory()->freelancer()->create();
        $intruder = User::factory()->freelancer()->create();

        $item = PortfolioItem::factory()->create([
            'freelancer_id' => $owner->id,
            'title' => 'Item protegido',
        ]);

        $this->actingAs($intruder)
            ->put(route('portfolio.update', $item), [
                'title' => 'Hackeado',
                'is_public' => '1',
            ])
            ->assertForbidden();

        $this->actingAs($intruder)
            ->post(route('portfolio.toggle', $item))
            ->assertForbidden();

        $this->assertSame('Item protegido', $item->fresh()->title);
    }

    public function test_client_cannot_access_portfolio(): void
    {
        $client = User::factory()->cliente()->create();

        $this->actingAs($client)
            ->get(route('portfolio.index'))
            ->assertForbidden();

        $this->actingAs($client)
            ->post(route('portfolio.store'), ['title' => 'Indevido'])
            ->assertForbidden();
    }
}
