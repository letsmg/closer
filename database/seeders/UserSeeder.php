<?php

namespace Database\Seeders;

use App\Enums\UserLevel;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuários staff (admin e operational)
        $this->createStaffUsers();
        
        // Criar usuários de cada nível não-staff
        $this->createRegularUsers();
    }

    /**
     * Criar usuários staff (admin e operational)
     */
    private function createStaffUsers(): void
    {
        // Usuário Admin Principal
        User::create([
            'name' => 'Administrator Principal',
            'email' => 'admin@1.com',
            'password' => Hash::make('Mudar@123'),
            'nivel_acesso' => UserLevel::ADMIN->value,
            'email_verified_at' => now(),
            'ativo' => true,
            'uuid' => \Illuminate\Support\Str::ulid(),
        ]);

        // Usuário Operacional
        User::create([
            'name' => 'System Operator',
            'email' => 'operator@2.com',
            'password' => Hash::make('Mudar@123'),
            'nivel_acesso' => UserLevel::OPERATIONAL->value,
            'email_verified_at' => now(),
            'ativo' => true,
            'uuid' => \Illuminate\Support\Str::ulid(),
        ]);
    }

    /**
     * Criar usuários regulares de cada nível
     */
    private function createRegularUsers(): void
    {
        // Usuários FREE (nível 0) - 5 usuários
$freeUsers = [
    ['Free User', 'free@closer.com'],
    ['Free User 2', 'free2@closer.com'],
    ['Free User 3', 'free3@closer.com'],
    ['Free User 4', 'free4@closer.com'],
    ['Free User 5', 'free5@closer.com'],
    ['Free User 6', 'free6@closer.com'],
    ['Free User 7', 'free7@closer.com'],
];

        foreach ($freeUsers as [$name, $email]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => UserLevel::FREE->value,
                'email_verified_at' => now(),
                'ativo' => true,
                'uuid' => \Illuminate\Support\Str::ulid(),
            ]);
        }

        // Usuários PLUS (nível 1) - 4 usuários
$plusUsers = [
    ['Plus User', 'plus@closer.com'],
    ['Plus User 2', 'plus2@closer.com'],
    ['Plus User 3', 'plus3@closer.com'],
    ['Plus User 4', 'plus4@closer.com'],
    ['Plus User 5', 'plus5@closer.com'],
    ['Plus User 6', 'plus6@closer.com'],
    ['Plus User 7', 'plus7@closer.com'],
];

        foreach ($plusUsers as [$name, $email]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => UserLevel::PLUS->value,
                'email_verified_at' => now(),
                'ativo' => true,
                'uuid' => \Illuminate\Support\Str::ulid(),
            ]);
        }

        // Usuários PREMIUM (nível 2) - 4 usuários
$premiumUsers = [
    ['Premium User', 'premium@closer.com'],
    ['Premium User 2', 'premium2@closer.com'],
    ['Premium User 3', 'premium3@closer.com'],
    ['Premium User 4', 'premium4@closer.com'],
    ['Premium User 5', 'premium5@closer.com'],
    ['Premium User 6', 'premium6@closer.com'],
    ['Premium User 7', 'premium7@closer.com'],
];

        foreach ($premiumUsers as [$name, $email]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => UserLevel::PREMIUM->value,
                'email_verified_at' => now(),
                'ativo' => true,
                'uuid' => \Illuminate\Support\Str::ulid(),
            ]);
        }
    }

    /**
     * Retorna informações dos usuários criados para debug
     */
    public function getUsersInfo(): array
    {
        return [
            'staff' => [
                'admin' => [
                    'email' => '1@1.com',
                    'password' => 'Mudar@123',
                    'level' => 'Admin (3)',
                ],
                'operador' => [
                    'email' => '2@2.com',
                    'password' => 'Mudar@123',
                    'level' => 'Operational (4)',
                ],
            ],
            'free_users' => [
                'emails' => [
    'free@closer.com',
    'free2@closer.com',
    'free3@closer.com',
    'free4@closer.com',
    'free5@closer.com',
    'free6@closer.com',
    'free7@closer.com',
],
                'password' => 'Mudar@123',
                'level' => 'Free (0)',
                'count' => 7,
'count' => 7,
            ],
            'plus_users' => [
                'emails' => [
    'plus@closer.com',
    'plus2@closer.com',
    'plus3@closer.com',
    'plus4@closer.com',
    'plus5@closer.com',
    'plus6@closer.com',
    'plus7@closer.com',
],
                'password' => 'Mudar@123',
                'level' => 'Plus (1)',
                'count' => 1,
            ],
            'premium_users' => [
                'emails' => [
    'premium@closer.com',
    'premium2@closer.com',
    'premium3@closer.com',
    'premium4@closer.com',
    'premium5@closer.com',
    'premium6@closer.com',
    'premium7@closer.com',
],
                'password' => 'Mudar@123',
                'level' => 'Premium (2)',
                'count' => 7,
            ],
            'note' => 'Todas as senhas usam Argon2id (configuração padrão Laravel)',
        ];
    }
}
