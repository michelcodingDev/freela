<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_payments_filters_by_status_and_search(): void
    {
        $admin = $this->admin();
        $custody = $this->paymentWithProject('Projeto em custódia', 'custodia');
        $this->paymentWithProject('Projeto liberado', 'liberado');

        $this->actingAs($admin)
            ->get(route('admin.payments', ['status' => 'custodia']))
            ->assertOk()
            ->assertSee('Projeto em custódia')
            ->assertDontSee('Projeto liberado');

        $this->actingAs($admin)
            ->get(route('admin.payments', ['search' => $custody->project->title]))
            ->assertOk()
            ->assertSee($custody->project->title);
    }

    public function test_admin_payments_summary_uses_real_values(): void
    {
        $admin = $this->admin();
        $this->paymentWithProject('Projeto em custódia', 'custodia', 1234.56);

        $this->actingAs($admin)
            ->get(route('admin.payments'))
            ->assertOk()
            ->assertSee('R$ 1.234,56');
    }

    public function test_payments_csv_export(): void
    {
        $admin = $this->admin();
        $payment = $this->paymentWithProject('Projeto exportado', 'liberado');

        $response = $this->actingAs($admin)->get(route('admin.reports.payments'));

        $response->assertOk();
        $this->assertStringContainsString('Projeto exportado', $response->streamedContent());
        $this->assertStringContainsString($payment->client->name, $response->streamedContent());
    }

    public function test_users_and_projects_csv_export(): void
    {
        $admin = $this->admin();
        $payment = $this->paymentWithProject('Projeto do relatório', 'custodia');

        $users = $this->actingAs($admin)->get(route('admin.reports.users'));
        $users->assertOk();
        $this->assertStringContainsString($payment->client->email, $users->streamedContent());

        $projects = $this->actingAs($admin)->get(route('admin.reports.projects'));
        $projects->assertOk();
        $this->assertStringContainsString('Projeto do relatório', $projects->streamedContent());
    }

    public function test_non_admin_cannot_export_reports(): void
    {
        $client = User::factory()->cliente()->create();

        $this->actingAs($client)
            ->get(route('admin.reports.payments'))
            ->assertForbidden();
    }

    private function admin(): User
    {
        $admin = User::factory()->admin()->create();
        $admin->assignRole('admin');

        return $admin;
    }

    private function paymentWithProject(string $title, string $status, float $amount = 1000): Payment
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'title' => $title,
            'client_id' => $client->id,
            'category_id' => $category->id,
        ]);

        return Payment::factory()->create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => $status,
            'amount' => $amount,
        ]);
    }
}
