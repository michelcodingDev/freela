<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'description' => 'Plano gratuito para começar na plataforma',
                'price_monthly' => 0.00,
                'commission_percentage' => 20.00,
                'min_commission' => 5.00,
                'proposals_per_month' => 10,
                'max_portfolio_items' => 3,
                'withdrawal_days' => 6,
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'description' => 'Plano para freelancers que querem mais visibilidade',
                'price_monthly' => 49.90,
                'commission_percentage' => 15.00,
                'min_commission' => 10.00,
                'proposals_per_month' => 50,
                'max_portfolio_items' => 12,
                'withdrawal_days' => 4,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Premium',
                'slug' => 'premium',
                'description' => 'Plano completo com máxima prioridade e menores taxas',
                'price_monthly' => 89.90,
                'commission_percentage' => 10.00,
                'min_commission' => 10.00,
                'proposals_per_month' => 999,
                'max_portfolio_items' => 15,
                'withdrawal_days' => 2,
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::create($plan);
        }
    }
}
