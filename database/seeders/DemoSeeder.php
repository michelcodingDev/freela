<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PortfolioItem;
use App\Models\Project;
use App\Models\Proposal;
use App\Models\Review;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DemoSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        if (User::where('email', 'cliente@demo.test')->exists()) {
            return;
        }

        $categories = Category::active()->orderBy('sort_order')->get();
        $firstCategory = $categories->first();

        $skills = collect(['Laravel', 'Vue.js', 'PHP', 'Design UI', 'Figma'])
            ->map(fn ($name) => Skill::firstOrCreate(
                ['name' => $name, 'category_id' => $firstCategory->id],
                ['slug' => Str::slug($name)]
            ));

        $cliente = User::factory()->cliente()->create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@demo.test',
        ]);
        $cliente->assignRole('cliente');
        $cliente->profile()->create([
            'city' => 'São Paulo', 'state' => 'SP',
            'professional_title' => 'Empresa Demo',
            'bio' => 'Contratante para desenvolvimento web.',
        ]);

        $freelancer = User::factory()->freelancer()->create([
            'name' => 'Freelancer Demo',
            'email' => 'freelancer@demo.test',
        ]);
        $freelancer->assignRole('freelancer');
        $freelancer->profile()->create([
            'city' => 'Curitiba', 'state' => 'PR',
            'professional_title' => 'Desenvolvedor Full-Stack',
            'bio' => 'Especialista em Laravel e Vue, 5 anos de experiência.',
            'pix_key' => 'freelancer@demo.test',
        ]);

        PortfolioItem::factory()->create(['freelancer_id' => $freelancer->id, 'title' => 'E-commerce Laravel']);

        $project = Project::factory()->create([
            'client_id' => $cliente->id,
            'category_id' => $firstCategory->id,
            'title' => 'Loja virtual Laravel',
            'description' => 'Desenvolver loja virtual com Laravel, pagamento Pix e painel admin. Preciso de PHP, Laravel e frontend responsivo.',
            'status' => 'aguardando_propostas',
            'experience_level' => 'especialista',
            'budget_min' => 3000,
            'budget_max' => 6000,
        ]);
        $project->skills()->attach($skills->take(3)->pluck('id')->all());

        Proposal::factory()->create([
            'project_id' => $project->id,
            'freelancer_id' => $freelancer->id,
            'offer' => 4500,
            'final_offer' => 4500,
            'estimated_days' => 21,
        ]);

        // Projeto concluído para dashboard mostrar histórico
        $done = Project::factory()->create([
            'client_id' => $cliente->id,
            'category_id' => $firstCategory->id,
            'title' => 'Site institucional',
            'status' => 'concluido',
            'completed_at' => now()->subDays(3),
        ]);
        Proposal::factory()->accepted()->create([
            'project_id' => $done->id,
            'freelancer_id' => $freelancer->id,
        ]);
        Payment::factory()->released()->create([
            'project_id' => $done->id,
            'client_id' => $cliente->id,
            'freelancer_id' => $freelancer->id,
            'amount' => 2000,
            'net_amount' => 1560,
        ]);
        Review::factory()->create([
            'project_id' => $done->id,
            'from_user_id' => $cliente->id,
            'to_user_id' => $freelancer->id,
            'rating' => 5,
        ]);

        Notification::factory()->create([
            'user_id' => $cliente->id,
            'type' => 'proposta_recebida',
            'title' => 'Nova proposta',
            'message' => 'Você recebeu uma proposta no projeto Loja virtual Laravel.',
        ]);
    }
}
