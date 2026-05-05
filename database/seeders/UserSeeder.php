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
                'emails' => ['free@closer.com'],
                'password' => 'Mudar@123',
                'level' => 'Free (0)',
                'count' => 1,
            ],
            'plus_users' => [
                'emails' => ['plus@closer.com'],
                'password' => 'Mudar@123',
                'level' => 'Plus (1)',
                'count' => 1,
            ],
            'premium_users' => [
                'emails' => ['premium@closer.com'],
                'password' => 'Mudar@123',
                'level' => 'Premium (2)',
                'count' => 4,
            ],
            'note' => 'Todas as senhas usam Argon2id (configuração padrão Laravel)',
        ];
    }
}
