<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\PlanFeature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanFeatureSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $freePlan = Plan::where('slug', 'free')->first();
        $proPlan = Plan::where('slug', 'pro')->first();
        $premiumPlan = Plan::where('slug', 'premium')->first();

        $features = [
            // Free plan features
            ['plan_id' => $freePlan->id, 'name' => 'Até 10 propostas por mês', 'description' => 'Limite de propostas para projetos', 'is_included' => true],
            ['plan_id' => $freePlan->id, 'name' => 'Até 3 itens no portfólio', 'description' => 'Limite de trabalhos no portfólio', 'is_included' => true],
            ['plan_id' => $freePlan->id, 'name' => 'Saque em até 6 dias úteis', 'description' => 'Prazo para recebimento após liberação', 'is_included' => true],
            ['plan_id' => $freePlan->id, 'name' => 'Taxa de 20% de comissão', 'description' => 'Comissão da plataforma sobre cada projeto', 'is_included' => true],
            ['plan_id' => $freePlan->id, 'name' => 'Proposta promovida', 'description' => 'Destacar sua proposta nos projetos', 'is_included' => false],

            // Pro plan features
            ['plan_id' => $proPlan->id, 'name' => 'Até 50 propostas por mês', 'description' => 'Limite maior de propostas', 'is_included' => true],
            ['plan_id' => $proPlan->id, 'name' => 'Até 12 itens no portfólio', 'description' => 'Mais espaço para seus trabalhos', 'is_included' => true],
            ['plan_id' => $proPlan->id, 'name' => 'Saque em até 4 dias úteis', 'description' => 'Receba mais rápido', 'is_included' => true],
            ['plan_id' => $proPlan->id, 'name' => 'Taxa de 15% de comissão', 'description' => 'Menor comissão da plataforma', 'is_included' => true],
            ['plan_id' => $proPlan->id, 'name' => '1 proposta promovida por projeto', 'description' => 'Destaque sua proposta', 'is_included' => true],

            // Premium plan features
            ['plan_id' => $premiumPlan->id, 'name' => 'Propostas ilimitadas', 'description' => 'Sem limite de propostas', 'is_included' => true],
            ['plan_id' => $premiumPlan->id, 'name' => 'Até 15 itens no portfólio', 'description' => 'Portfólio completo', 'is_included' => true],
            ['plan_id' => $premiumPlan->id, 'name' => 'Saque em até 2 dias úteis', 'description' => 'Receba o mais rápido possível', 'is_included' => true],
            ['plan_id' => $premiumPlan->id, 'name' => 'Taxa de 10% de comissão', 'description' => 'Menor comissão da plataforma', 'is_included' => true],
            ['plan_id' => $premiumPlan->id, 'name' => 'Exclusividade 24h em novos projetos', 'description' => 'Veja projetos antes dos outros planos', 'is_included' => true],
        ];

        foreach ($features as $feature) {
            PlanFeature::create($feature);
        }
    }
}
