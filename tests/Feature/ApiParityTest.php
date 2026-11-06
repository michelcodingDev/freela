<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PortfolioItem;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\User;
use App\Models\Withdrawal;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ApiParityTest extends TestCase
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

    public function test_api_can_cancel_own_pending_withdrawal(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $token = $this->tokenFor($freelancer);

        $withdrawal = Withdrawal::factory()->create([
            'freelancer_id' => $freelancer->id,
            'status' => 'solicitado',
        ]);

        $this->withToken($token)
            ->postJson(route('api.withdrawals.cancel', $withdrawal))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertSame('cancelado', $withdrawal->fresh()->status);
    }

    public function test_api_cannot_cancel_others_withdrawal(): void
    {
        $owner = User::factory()->freelancer()->create();
        $other = User::factory()->freelancer()->create();
        $token = $this->tokenFor($other);

        $withdrawal = Withdrawal::factory()->create([
            'freelancer_id' => $owner->id,
            'status' => 'solicitado',
        ]);

        $this->withToken($token)
            ->postJson(route('api.withdrawals.cancel', $withdrawal))
            ->assertNotFound();

        $this->assertSame('solicitado', $withdrawal->fresh()->status);
    }

    public function test_api_password_change_requires_current_and_confirms(): void
    {
        $user = User::factory()->cliente()->create();
        $token = $this->tokenFor($user);

        $this->withToken($token)
            ->putJson(route('api.profile.password.update'), [
                'current_password' => 'password',
                'password' => 'nova-senha-api-123',
                'password_confirmation' => 'nova-senha-api-123',
            ])
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertTrue(Hash::check('nova-senha-api-123', $user->fresh()->password));
    }

    public function test_api_freelancer_show_hides_private_content(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $token = $this->tokenFor($client);
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'concluido',
        ]);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'comment' => 'Avaliação privada secreta.',
            'is_public' => false,
        ]);

        PortfolioItem::factory()->create([
            'freelancer_id' => $freelancer->id,
            'title' => 'Item privado API',
            'is_public' => false,
        ]);

        $response = $this->withToken($token)
            ->getJson(route('api.freelancers.show', $freelancer))
            ->assertOk()
            ->assertJsonPath('rating_count', 0);

        $this->assertStringNotContainsString(
            'Avaliação privada secreta.',
            $response->getContent()
        );
        $this->assertStringNotContainsString(
            'Item privado API',
            $response->getContent()
        );
    }

    public function test_api_dispute_requires_executing_project_and_notifies_other_party(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create();
        $token = $this->tokenFor($client);
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);

        Proposal::factory()->accepted()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
        ]);

        $this->withToken($token)
            ->postJson(route('api.projects.disputes.store', $project), [
                'reason' => 'Motivo detalhado para testar a guarda da API.',
                'argument' => 'Argumento do cliente.',
            ])
            ->assertStatus(422);

        $project->update(['status' => 'em_andamento']);

        $this->withToken($token)
            ->postJson(route('api.projects.disputes.store', $project), [
                'reason' => 'Motivo detalhado para testar a guarda da API.',
                'argument' => 'Argumento do cliente.',
            ])
            ->assertCreated()
            ->assertJsonPath('success', true);

        $this->assertSame('disputa', $project->fresh()->status);
        $this->assertDatabaseHas('notifications', [
            'user_id' => $freelancer->id,
            'type' => 'disputa_aberta',
        ]);

        $this->withToken($token)
            ->postJson(route('api.projects.disputes.store', $project), [
                'reason' => 'Segunda disputa não pode existir.',
            ])
            ->assertStatus(422);
    }
}
