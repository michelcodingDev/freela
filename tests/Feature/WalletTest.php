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

class WalletTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_cannot_access_wallet(): void
    {
        $this->get(route('wallet.index'))->assertRedirect(route('login'));
    }

    public function test_freelancer_sees_balance_and_statement(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'concluido',
        ]);

        Payment::factory()->released()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'amount' => 1000,
            'platform_fee' => 100,
            'net_amount' => 900,
        ]);

        Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'amount' => 100,
            'fee' => 2,
            'net_amount' => 98,
            'status' => 'solicitado',
        ]);

        $this->actingAs($freelancer)
            ->get(route('wallet.index'))
            ->assertOk()
            ->assertSee('Carteira e Extrato')
            ->assertSee('R$ 802,00')
            ->assertSee('Recebimento: '.$project->title)
            ->assertSee('Saque Pix');
    }

    public function test_client_wallet_shows_escrow_totals(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'em_andamento',
        ]);

        Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'amount' => 500,
            'status' => 'custodia',
        ]);

        $this->actingAs($client)
            ->get(route('wallet.index'))
            ->assertOk()
            ->assertSee('Em custódia')
            ->assertSee('R$ 500,00')
            ->assertSee('Pagamento: '.$project->title);
    }

    public function test_freelancer_can_export_statement_csv(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'concluido',
        ]);

        Payment::factory()->released()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'net_amount' => 450,
        ]);

        $response = $this->actingAs($freelancer)->get(route('wallet.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Recebimento: '.$project->title, $response->streamedContent());
        $this->assertStringContainsString('450,00', $response->streamedContent());
    }

    public function test_admin_is_redirected_to_admin_payments(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('wallet.index'))
            ->assertRedirect(route('admin.payments'));
    }
}
