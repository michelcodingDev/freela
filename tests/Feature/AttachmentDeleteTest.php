<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttachmentDeleteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_owner_can_delete_attachment(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);

        $attachment = ProjectAttachment::create([
            'project_id' => $project->id,
            'file_path' => 'project_attachments/arquivo.pdf',
            'original_name' => 'arquivo.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ]);

        $this->actingAs($client)
            ->delete(route('attachments.destroy', $attachment))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('project_attachments', ['id' => $attachment->id]);
    }

    public function test_non_owner_cannot_delete_attachment(): void
    {
        $client = User::factory()->cliente()->create();
        $outsider = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'aguardando_propostas',
        ]);

        $attachment = ProjectAttachment::create([
            'project_id' => $project->id,
            'file_path' => 'project_attachments/arquivo.pdf',
            'original_name' => 'arquivo.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ]);

        $this->actingAs($outsider)
            ->delete(route('attachments.destroy', $attachment))
            ->assertForbidden();

        $this->assertDatabaseHas('project_attachments', ['id' => $attachment->id]);
    }

    public function test_cannot_delete_attachment_of_completed_project(): void
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();

        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'status' => 'concluido',
        ]);

        $attachment = ProjectAttachment::create([
            'project_id' => $project->id,
            'file_path' => 'project_attachments/arquivo.pdf',
            'original_name' => 'arquivo.pdf',
            'mime_type' => 'application/pdf',
            'file_size' => 1024,
        ]);

        $this->actingAs($client)
            ->delete(route('attachments.destroy', $attachment))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('project_attachments', ['id' => $attachment->id]);
    }
}
