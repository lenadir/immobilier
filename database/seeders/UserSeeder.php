<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin principal
        User::firstOrCreate(
            ['email' => 'admin@immobilier.dz'],
            [
                'name'      => 'Administrateur',
                'password'  => Hash::make('Admin@12345'),
                'role'      => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        // Agents de démonstration
        $agents = [
            ['name' => 'Karim Benamara',  'email' => 'karim@immobilier.dz'],
            ['name' => 'Sarah Meziane',   'email' => 'sarah@immobilier.dz'],
            ['name' => 'Walid Bouazza',   'email' => 'walid@immobilier.dz'],
        ];

        foreach ($agents as $agent) {
            User::firstOrCreate(
                ['email' => $agent['email']],
                [
                    'name'      => $agent['name'],
                    'password'  => Hash::make('Agent@12345'),
                    'role'      => User::ROLE_AGENT,
                    'is_active' => true,
                ]
            );
        }

        // Guest de démonstration
        User::firstOrCreate(
            ['email' => 'visiteur@example.com'],
            [
                'name'      => 'Visiteur Test',
                'password'  => Hash::make('Guest@12345'),
                'role'      => User::ROLE_GUEST,
                'is_active' => true,
            ]
        );

        $this->command->info('✓ Utilisateurs créés avec succès.');
    }
}
