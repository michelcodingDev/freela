<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            [
                'name' => 'Web, Mobile e Software',
                'slug' => 'web-mobile-e-software',
                'icon' => 'code-2',
                'color' => '#3b82f6',
                'description' => 'Desenvolvimento de sites, aplicativos e software',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Design e Criação',
                'slug' => 'design-e-criacao',
                'icon' => 'palette',
                'color' => '#ec4899',
                'description' => 'Design gráfico, UX/UI, logotipos e criação',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Escrita e Tradução',
                'slug' => 'escrita-e-traducao',
                'icon' => 'file-text',
                'color' => '#10b981',
                'description' => 'Redação, copywriting, tradução e revisão',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Marketing e Vendas',
                'slug' => 'marketing-e-vendas',
                'icon' => 'megaphone',
                'color' => '#f59e0b',
                'description' => 'Marketing digital, SEO, vendas e publicidade',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Áudio e Vídeo',
                'slug' => 'audio-e-video',
                'icon' => 'video',
                'color' => '#8b5cf6',
                'description' => 'Edição de vídeo, animação, locução e produção',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Administração e Negócios',
                'slug' => 'administracao-e-negocios',
                'icon' => 'briefcase',
                'color' => '#64748b',
                'description' => 'Consultoria, gestão, administração e finanças',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Suporte e Atendimento',
                'slug' => 'suporte-e-atendimento',
                'icon' => 'headphones',
                'color' => '#06b6d4',
                'description' => 'Suporte ao cliente, atendimento e help desk',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Outros',
                'slug' => 'outros',
                'icon' => 'more-horizontal',
                'color' => '#94a3b8',
                'description' => 'Outros serviços freelancer',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
