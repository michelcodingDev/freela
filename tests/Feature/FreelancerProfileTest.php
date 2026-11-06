<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\PortfolioItem;
use App\Models\Project;
use App\Models\Review;
use App\Models\Skill;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FreelancerProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_guest_can_view_public_freelancer_profile_with_summary(): void
    {
        $client = User::factory()->cliente()->create(['name' => 'Cliente Avaliador']);
        $freelancer = User::factory()->freelancer()->create(['name' => 'Ana Dev']);
        $freelancer->profile()->create([
            'professional_title' => 'Desenvolvedora Full-stack',
            'city' => 'São Paulo',
            'state' => 'SP',
            'bio' => 'Construo sistemas Laravel.',
            'projects_completed' => 12,
            'identity_verified' => true,
        ]);

        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Loja virtual completa',
            'status' => 'concluido',
        ]);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'rating' => 5,
            'comment' => 'Trabalho impecável do início ao fim.',
            'is_public' => true,
        ]);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'rating' => 4,
            'comment' => 'Muito bom, entregou no prazo.',
            'is_public' => true,
        ]);

        PortfolioItem::factory()->create([
            'freelancer_id' => $freelancer->id,
            'title' => 'E-commerce Laravel',
            'description' => 'Loja com pagamento em custódia.',
            'is_public' => true,
        ]);

        $this->get(route('freelancers.show', $freelancer))
            ->assertOk()
            ->assertSee('Ana Dev')
            ->assertSee('Desenvolvedora Full-stack')
            ->assertSee('São Paulo / SP')
            ->assertSee('Identidade verificada')
            ->assertSee('Avaliações')
            ->assertSee('Trabalho impecável')
            ->assertSee('Loja virtual completa')
            ->assertSee('E-commerce Laravel')
            ->assertSee('Contratar freelancer')
            ->assertSee('4,5');
    }

    public function test_private_reviews_and_portfolio_items_are_hidden(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create(['name' => 'Bruno Designer']);
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'title' => 'Projeto confidencial',
            'status' => 'concluido',
        ]);

        Review::factory()->create([
            'project_id' => $project->id,
            'from_user_id' => $client->id,
            'to_user_id' => $freelancer->id,
            'comment' => 'Comentário privado que não deve aparecer.',
            'is_public' => false,
        ]);

        PortfolioItem::factory()->create([
            'freelancer_id' => $freelancer->id,
            'title' => 'Arquivo interno secreto',
            'is_public' => false,
        ]);

        $this->get(route('freelancers.show', $freelancer))
            ->assertOk()
            ->assertDontSee('Comentário privado que não deve aparecer.')
            ->assertDontSee('Arquivo interno secreto')
            ->assertSee('Nenhuma avaliação pública ainda');
    }

    public function test_client_sees_cta_to_publish_project_and_freelancer_sees_edit_cta(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create(['name' => 'Carla Dev']);

        $this->actingAs($client)
            ->get(route('freelancers.show', $freelancer))
            ->assertOk()
            ->assertSee('Publicar projeto')
            ->assertSee(route('projects.create'), false);

        $this->actingAs($freelancer)
            ->get(route('freelancers.show', $freelancer))
            ->assertOk()
            ->assertSee('Editar meu perfil');
    }

    public function test_non_freelancer_and_inactive_freelancer_profile_returns_not_found(): void
    {
        $client = User::factory()->cliente()->create();
        $inactive = User::factory()->freelancer()->create(['status' => 'suspenso']);

        $this->get(route('freelancers.show', $client))->assertNotFound();
        $this->get(route('freelancers.show', $inactive))->assertNotFound();
    }

    public function test_index_shows_verified_badge_skills_and_hides_private_review_stats(): void
    {
        $client = User::factory()->cliente()->create();
        $freelancer = User::factory()->freelancer()->create(['name' => 'Duda Dev']);
        $freelancer->profile()->create(['identity_verified' => true, 'city' => 'Curitiba', 'state' => 'PR']);
        $skill = Skill::factory()->create(['name' => 'Laravel']);
        $freelancer->skills()->attach($skill->id);

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
            'rating' => 1,
            'is_public' => false,
        ]);

        $this->get(route('freelancers.index'))
            ->assertOk()
            ->assertSee('Duda Dev')
            ->assertSee('Laravel')
            ->assertSee('Curitiba / PR')
            ->assertSee('0,0 (0)');
    }
}
