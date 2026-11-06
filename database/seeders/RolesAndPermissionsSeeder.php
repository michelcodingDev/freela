<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar permissões
        $permissions = [
            // Usuários
            'ver usuarios',
            'editar usuarios',
            'banir usuarios',
            // Projetos
            'criar projetos',
            'editar projetos',
            'aprovar projetos',
            'fechar projetos',
            'ver todos projetos',
            // Propostas
            'enviar propostas',
            'aceitar propostas',
            'rejeitar propostas',
            // Pagamentos
            'fazer pagamentos',
            'liberar pagamentos',
            'ver todos pagamentos',
            // Planos
            'gerenciar planos',
            // Disputas
            'abrir disputas',
            'resolver disputas',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Criar roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $clienteRole = Role::firstOrCreate(['name' => 'cliente']);
        $freelancerRole = Role::firstOrCreate(['name' => 'freelancer']);

        // Atribuir permissões ao Admin (todas)
        $adminRole->syncPermissions(Permission::all());

        // Atribuir permissões ao Cliente
        $clienteRole->syncPermissions([
            'ver usuarios',
            'criar projetos',
            'editar projetos',
            'fechar projetos',
            'ver todos projetos',
            'enviar propostas',
            'aceitar propostas',
            'rejeitar propostas',
            'fazer pagamentos',
            'liberar pagamentos',
            'abrir disputas',
        ]);

        // Atribuir permissões ao Freelancer
        $freelancerRole->syncPermissions([
            'ver usuarios',
            'enviar propostas',
            'ver todos projetos',
            'ver todos pagamentos', // só os seus
        ]);

        // Criar usuário admin padrão
        $admin = User::firstOrCreate(
            ['email' => 'admin@freelajob.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('admin123'),
                'type' => 'admin',
                'status' => 'ativo',
            ]
        );
        if (! $admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        // Criar perfil do admin
        $admin->profile()->create([
            'city' => 'São Paulo',
            'state' => 'SP',
            'professional_title' => 'Administrador da Plataforma',
            'rating_average' => 5.00,
            'total_reviews' => 0,
            'total_earned' => 0.00,
            'projects_completed' => 0,
        ]);
    }
}
