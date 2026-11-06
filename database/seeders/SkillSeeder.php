<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Skill;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $map = [
            'web-mobile-e-software' => ['Laravel', 'PHP', 'Vue.js', 'React', 'MySQL', 'REST API', 'WordPress'],
            'design-e-criacao' => ['Figma', 'Photoshop', 'UI/UX', 'Branding', 'Illustrator'],
            'escrita-e-traducao' => ['Redação', 'Copywriting', 'Tradução', 'SEO'],
            'marketing-e-vendas' => ['SEO', 'Google Ads', 'Meta Ads', 'Social Media'],
            'audio-e-video' => ['Edição de Vídeo', 'Motion Design', 'Locução'],
            'administracao-e-negocios' => ['Gestão Financeira', 'Consultoria', 'Planilhas'],
            'suporte-e-atendimento' => ['Atendimento ao Cliente', 'Help Desk'],
            'outros' => ['Virtual Assistant'],
        ];

        foreach ($map as $slug => $names) {
            $category = Category::where('slug', $slug)->first();
            if (! $category) {
                continue;
            }
            foreach ($names as $name) {
                Skill::firstOrCreate(
                    ['name' => $name, 'category_id' => $category->id],
                    ['slug' => Str::slug($name).'-'.Str::random(6)]
                );
            }
        }
    }
}
