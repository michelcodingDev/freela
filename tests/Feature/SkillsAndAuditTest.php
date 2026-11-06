<?php

namespace Tests\Feature;

use App\Models\Skill;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SkillsAndAuditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_freelancer_saves_skills_via_profile(): void
    {
        $freelancer = User::factory()->freelancer()->create();
        $a = Skill::factory()->create();
        $b = Skill::factory()->create();

        $this->actingAs($freelancer)
            ->put(route('profile.update'), [
                'name' => $freelancer->name,
                'email' => $freelancer->email,
                'skills' => [$a->id, $b->id],
            ])
            ->assertRedirect(route('profile.show'));

        $this->assertEquals(2, $freelancer->skills()->count());
    }

    public function test_freelancers_filter_by_skill(): void
    {
        $viewer = User::factory()->cliente()->create();
        $skill = Skill::factory()->create(['name' => 'Laravel Pro']);
        $matching = User::factory()->freelancer()->create(['name' => 'Ana Match']);
        $matching->skills()->attach($skill->id);
        User::factory()->freelancer()->create(['name' => 'Joao NoMatch']);

        $this->actingAs($viewer)
            ->get(route('freelancers.index', ['skill' => $skill->id]))
            ->assertOk()
            ->assertSee('Ana Match')
            ->assertDontSee('Joao NoMatch');
    }
}
