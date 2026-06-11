<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Project;
use App\Models\Ticket;
use App\Models\TicketDetail;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(RolesAndPermissionsSeeder::class);

        $admin = User::create([
            'name' => 'Admin ServiceHub',
            'email' => 'admin@servicehub.local',
            'password' => null,
        ]);
        UserProfile::create(['user_id' => $admin->id, 'phone' => '(11) 99999-0000', 'position' => 'Administrador']);
        $admin->assignRole('admin');

        $manager = User::create([
            'name' => 'Gerente Teste',
            'email' => 'manager@servicehub.local',
            'password' => null,
        ]);
        UserProfile::create(['user_id' => $manager->id, 'position' => 'Gerente de Projetos']);
        $manager->assignRole('manager');

        $agent = User::create([
            'name' => 'Agente Teste',
            'email' => 'agent@servicehub.local',
            'password' => null,
        ]);
        UserProfile::create(['user_id' => $agent->id, 'position' => 'Suporte Técnico']);
        $agent->assignRole('agent');

        $companies = Company::factory(3)->create();

        foreach ($companies as $company) {
            $projects = Project::factory(2)->create(['company_id' => $company->id]);

            foreach ($projects as $project) {
                $tickets = Ticket::factory(3)->create([
                    'project_id' => $project->id,
                    'user_id' => $agent->id,
                ]);

                foreach ($tickets as $ticket) {
                    TicketDetail::create([
                        'ticket_id' => $ticket->id,
                        'technical_notes' => null,
                        'additional_data' => null,
                        'processed_at' => null,
                    ]);
                }
            }
        }
    }
}
