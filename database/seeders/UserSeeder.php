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
        // Criar usuários staff
        $this->createStaffUsers();
        
        // Criar usuários de cada nível não-staff
        $this->createRegularUsers();
    }

    /**
     * Criar usuários staff
     */
    private function createStaffUsers(): void
    {
        // Admin Principal
        User::create([
            'name' => 'Administrator Principal',
            'email' => 'admin@1.com',
            'password' => Hash::make('Mudar@123'),
            'nivel_acesso' => UserLevel::ADMIN->value,
            'email_verified_at' => now(),
            'ativo' => true,
            'uuid' => \Illuminate\Support\Str::ulid(),
        ]);

        // Operacional
        User::create([
            'name' => 'System Operator',
            'email' => 'operator@2.com',
            'password' => Hash::make('Mudar@123'),
            'nivel_acesso' => UserLevel::OPERATIONAL->value,
            'email_verified_at' => now(),
            'ativo' => true,
            'uuid' => \Illuminate\Support\Str::ulid(),
        ]);

        // Suporte
        User::create([
            'name' => 'Support Agent',
            'email' => 'support@3.com',
            'password' => Hash::make('Mudar@123'),
            'nivel_acesso' => UserLevel::SUPPORT->value,
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
        // FREE (nível 0) - 3 usuários
        foreach ([
            ['Free User', 'free@closer.com'],
            ['Free User 2', 'free2@closer.com'],
            ['Free User 3', 'free3@closer.com'],
        ] as [$name, $email]) {
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

        // MODERATOR (nível 1) - 2 usuários
        foreach ([
            ['Moderator User', 'moderator@closer.com'],
            ['Moderator User 2', 'moderator2@closer.com'],
        ] as [$name, $email]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => UserLevel::MODERATOR->value,
                'email_verified_at' => now(),
                'ativo' => true,
                'uuid' => \Illuminate\Support\Str::ulid(),
            ]);
        }

        // PLUS (nível 2) - 2 usuários
        foreach ([
            ['Plus User', 'plus@closer.com'],
            ['Plus User 2', 'plus2@closer.com'],
        ] as [$name, $email]) {
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

        // PREMIUM (nível 3) - 2 usuários
        foreach ([
            ['Premium User', 'premium@closer.com'],
            ['Premium User 2', 'premium2@closer.com'],
        ] as [$name, $email]) {
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

        // COFOUNDER (nível 4) - 2 usuários
        foreach ([
            ['Co-Founder User', 'cofounder@closer.com'],
            ['Co-Founder User 2', 'cofounder2@closer.com'],
        ] as [$name, $email]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => UserLevel::COFOUNDER->value,
                'email_verified_at' => now(),
                'ativo' => true,
                'uuid' => \Illuminate\Support\Str::ulid(),
            ]);
        }

        // ELITE (nível 5) - 2 usuários
        foreach ([
            ['Elite User', 'elite@closer.com'],
            ['Elite User 2', 'elite2@closer.com'],
        ] as [$name, $email]) {
            User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('Mudar@123'),
                'nivel_acesso' => UserLevel::ELITE->value,
                'email_verified_at' => now(),
                'ativo' => true,
                'uuid' => \Illuminate\Support\Str::ulid(),
            ]);
        }
    }
}