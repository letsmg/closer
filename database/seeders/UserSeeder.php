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
            'name' => 'Administrador Principal',
            'email' => '1@1.com',
            'password' => Hash::make('Mudar@123'),
            'nivel_acesso' => UserLevel::ADMIN->value,
            'email_verified_at' => now(),
            'ativo' => true,
            'uuid' => \Illuminate\Support\Str::ulid(),
        ]);

        // Usuário Operacional
        User::create([
            'name' => 'Operador do Sistema',
            'email' => '2@2.com',
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
            ['Ana Silva', 'ana.free@closer.com'],
            ['Carlos Santos', 'carlos.free@closer.com'],
            ['Mariana Costa', 'mariana.free@closer.com'],
            ['Pedro Oliveira', 'pedro.free@closer.com'],
            ['Lucas Ferreira', 'lucas.free@closer.com'],
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
            ['Roberto Almeida', 'roberto.plus@closer.com'],
            ['Fernanda Lima', 'fernanda.plus@closer.com'],
            ['Gustavo Gomes', 'gustavo.plus@closer.com'],
            ['Camila Dias', 'camila.plus@closer.com'],
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
            ['Ricardo Mendes', 'ricardo.premium@closer.com'],
            ['Patricia Castro', 'patricia.premium@closer.com'],
            ['Bruno Carvalho', 'bruno.premium@closer.com'],
            ['Tatiane Ribeiro', 'tatiane.premium@closer.com'],
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
                'emails' => ['ana.free@closer.com', 'carlos.free@closer.com', 'mariana.free@closer.com', 'pedro.free@closer.com', 'lucas.free@closer.com'],
                'password' => 'Mudar@123',
                'level' => 'Free (0)',
                'count' => 5,
            ],
            'plus_users' => [
                'emails' => ['roberto.plus@closer.com', 'fernanda.plus@closer.com', 'gustavo.plus@closer.com', 'camila.plus@closer.com'],
                'password' => 'Mudar@123',
                'level' => 'Plus (1)',
                'count' => 4,
            ],
            'premium_users' => [
                'emails' => ['ricardo.premium@closer.com', 'patricia.premium@closer.com', 'bruno.premium@closer.com', 'tatiane.premium@closer.com'],
                'password' => 'Mudar@123',
                'level' => 'Premium (2)',
                'count' => 4,
            ],
            'note' => 'Todas as senhas usam Argon2id (configuração padrão Laravel)',
        ];
    }
}
