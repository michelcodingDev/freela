<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use App\Models\Withdrawal;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletFilterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function freelancerWithStatement(): array
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();

        $recent = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto recente',
            'status' => 'concluido',
        ]);

        Payment::factory()->released()->create([
            'project_id' => $recent->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'amount' => 1000,
            'platform_fee' => 100,
            'net_amount' => 900,
            'created_at' => now(),
        ]);

        $old = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto antigo',
            'status' => 'concluido',
        ]);

        Payment::factory()->released()->create([
            'project_id' => $old->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'amount' => 2000,
            'platform_fee' => 200,
            'net_amount' => 1800,
            'created_at' => now()->subDays(120),
        ]);

        Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'amount' => 100,
            'fee' => 2,
            'net_amount' => 98,
            'status' => 'pago',
            'created_at' => now(),
        ]);

        return [$freelancer, $recent, $old];
    }

    public function test_direction_filter_shows_only_credit_or_debit(): void
    {
        [$freelancer, $recent] = $this->freelancerWithStatement();

        $this->actingAs($freelancer)
            ->get(route('wallet.index', ['direction' => 'credito']))
            ->assertOk()
            ->assertSee('Recebimento: '.$recent->title)
            ->assertDontSee('Saque Pix');

        $this->actingAs($freelancer)
            ->get(route('wallet.index', ['direction' => 'debito']))
            ->assertOk()
            ->assertSee('Saque Pix')
            ->assertDontSee('Recebimento: '.$recent->title);
    }

    public function test_status_filter_narrows_statement(): void
    {
        [$freelancer] = $this->freelancerWithStatement();

        $this->actingAs($freelancer)
            ->get(route('wallet.index', ['status' => 'pago']))
            ->assertOk()
            ->assertSee('Saque Pix')
            ->assertDontSee('Recebimento:');
    }

    public function test_period_filter_excludes_old_transactions(): void
    {
        [$freelancer, $recent, $old] = $this->freelancerWithStatement();

        $this->actingAs($freelancer)
            ->get(route('wallet.index', ['period' => '30d']))
            ->assertOk()
            ->assertSee('Recebimento: '.$recent->title)
            ->assertDontSee('Recebimento: '.$old->title);
    }

    public function test_export_respects_filters(): void
    {
        [$freelancer, $recent] = $this->freelancerWithStatement();

        $response = $this->actingAs($freelancer)
            ->get(route('wallet.export', ['direction' => 'credito']));

        $response->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString($recent->title, $content);
        $this->assertStringNotContainsString('Saque Pix', $content);
    }

    public function test_invalid_filter_values_fall_back_to_all(): void
    {
        [$freelancer, $recent] = $this->freelancerWithStatement();

        $this->actingAs($freelancer)
            ->get(route('wallet.index', ['direction' => 'invalido', 'period' => 'invalido']))
            ->assertOk()
            ->assertSee('Recebimento: '.$recent->title)
            ->assertSee('Saque Pix');
    }

    public function test_api_wallet_accepts_same_filters(): void
    {
        [$freelancer, $recent] = $this->freelancerWithStatement();
        $freelancer->forceFill(['api_token' => 'token-api-teste-'.str_repeat('e', 20)])->save();

        $this->withToken($freelancer->api_token)
            ->getJson(route('api.wallet.index', ['direction' => 'debito']))
            ->assertOk()
            ->assertJsonPath('data.filters.direction', 'debito')
            ->assertJsonFragment(['direction' => 'debito'])
            ->assertJsonMissing(['description' => 'Recebimento: '.$recent->title]);
    }
}
