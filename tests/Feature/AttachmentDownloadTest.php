<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Project;
use App\Models\ProjectAttachment;
use App\Models\ProjectInvite;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AttachmentDownloadTest extends TestCase
{
    use RefreshDatabase;

    private string $path = 'project_attachments/contrato.pdf';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        Storage::fake('public');
    }

    public function test_owner_can_download_attachment(): void
    {
        [$project, $client] = $this->privateProject();
        $attachment = $this->attachmentFor($project);

        $this->actingAs($client)
            ->get(route('attachments.download', $attachment))
            ->assertOk()
            ->assertDownload('contrato.pdf');
    }

    public function test_invited_freelancer_can_download_private_attachment(): void
    {
        [$project, $client] = $this->privateProject();
        $freelancer = User::factory()->freelancer()->create();
        ProjectInvite::create([
            'project_id' => $project->id,
            'client_id' => $client->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'convidado',
        ]);
        $attachment = $this->attachmentFor($project);

        $this->actingAs($freelancer)
            ->get(route('attachments.download', $attachment))
            ->assertOk();
    }

    public function test_unrelated_user_cannot_download_private_attachment(): void
    {
        [$project] = $this->privateProject();
        $intruder = User::factory()->freelancer()->create();
        $attachment = $this->attachmentFor($project);

        $this->actingAs($intruder)
            ->get(route('attachments.download', $attachment))
            ->assertForbidden();
    }

    public function test_admin_can_download_any_attachment(): void
    {
        [$project] = $this->privateProject();
        $admin = User::factory()->admin()->create();
        $attachment = $this->attachmentFor($project);

        $this->actingAs($admin)
            ->get(route('attachments.download', $attachment))
            ->assertOk();
    }

    public function test_missing_file_returns_not_found(): void
    {
        [$project, $client] = $this->privateProject();
        $attachment = ProjectAttachment::factory()->create([
            'project_id' => $project->id,
            'file_path' => 'project_attachments/inexistente.pdf',
        ]);

        $this->actingAs($client)
            ->get(route('attachments.download', $attachment))
            ->assertNotFound();
    }

    /**
     * @return array{0: Project, 1: User}
     */
    private function privateProject(): array
    {
        $client = User::factory()->cliente()->create();
        $category = Category::factory()->create();
        $project = Project::factory()->create([
            'client_id' => $client->id,
            'category_id' => $category->id,
            'visibility' => 'privado',
        ]);

        return [$project, $client];
    }

    private function attachmentFor(Project $project): ProjectAttachment
    {
        Storage::disk('public')->put($this->path, 'conteudo do contrato');

        return ProjectAttachment::factory()->create([
            'project_id' => $project->id,
            'file_path' => $this->path,
            'original_name' => 'contrato.pdf',
        ]);
    }
}
