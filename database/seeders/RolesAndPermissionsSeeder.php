<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'users.view'    => 'Ver usuários',
            'users.create'  => 'Criar usuários',
            'users.edit'    => 'Editar usuários',
            'users.delete'  => 'Excluir usuários',
            'users.promote' => 'Promover usuários',

            'companies.view'   => 'Ver empresas',
            'companies.create' => 'Criar empresas',
            'companies.edit'   => 'Editar empresas',
            'companies.delete' => 'Excluir empresas',

            'projects.view'   => 'Ver projetos',
            'projects.create' => 'Criar projetos',
            'projects.edit'   => 'Editar projetos',
            'projects.delete' => 'Excluir projetos',

            'tickets.view'   => 'Ver tickets',
            'tickets.create' => 'Criar tickets',
            'tickets.edit'   => 'Editar tickets',
            'tickets.delete' => 'Excluir tickets',
        ];

        foreach ($permissions as $name => $display) {
            Permission::firstOrCreate(['name' => $name], ['display_name' => $display]);
        }

        $allPermissions = Permission::all();

        // manager bypassa todos os gates via Gate::before (AppServiceProvider)
        Role::firstOrCreate(['name' => 'manager'], ['display_name' => 'Gerente'])
            ->permissions()->sync($allPermissions);

        // admin: acesso total exceto exclusão de usuários
        $adminPerms = Permission::whereNotIn('name', ['users.delete'])->pluck('id');
        Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrador'])
            ->permissions()->sync($adminPerms);

        $agentPerms = Permission::whereIn('name', [
            'tickets.view', 'tickets.create', 'tickets.edit',
            'companies.view', 'projects.view',
        ])->pluck('id');
        Role::firstOrCreate(['name' => 'agent'], ['display_name' => 'Agente'])
            ->permissions()->sync($agentPerms);
    }
}
