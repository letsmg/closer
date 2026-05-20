<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserTermsAcceptance;
use Illuminate\Database\Seeder;

/**
 * Seeder para popular a tabela `user_terms_acceptances`
 * 
 * Garante que todos os usuários existentes tenham um aceite válido
 * dos Termos de Uso e Política de Privacidade vigentes.
 */
class UserTermsAcceptanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $termsVersion = config('terms.version', '2026-05-20');
        $privacyVersion = config('terms.privacy_version', '2026-05-20');

        $users = User::all();

        foreach ($users as $user) {
            // Verifica se já existe um aceite válido para este usuário
            $hasAcceptance = UserTermsAcceptance::hasValidAcceptance(
                $user->id,
                $termsVersion,
                $privacyVersion
            );

            if (!$hasAcceptance) {
                UserTermsAcceptance::recordAcceptance(
                    $user->id,
                    $termsVersion,
                    $privacyVersion,
                    '127.0.0.1'
                );
            }
        }

        $this->command->info("Aceites de termos registrados para " . $users->count() . " usuários.");
    }
}
