<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\User;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some non-staff users to be reporters and reported
        $users = User::where('nivel_acesso', '<', 3)->get();

        if ($users->count() < 8) {
            // Create some more users if needed
            User::factory(10)->create([
                'nivel_acesso' => 1
            ]);
            $users = User::where('nivel_acesso', '<', 3)->get();
        }

        $reasons = ['harassment', 'disrespect', 'fake_profile', 'other'];
        $descriptions = [
            'harassment' => 'Este usuário continua me enviando mensagens mesmo depois de eu ter dito que não estava interessado.',
            'disrespect' => 'O usuário foi extremamente grosseiro e usou palavreado ofensivo durante nossa conversa.',
            'fake_profile' => 'As fotos deste perfil parecem ser de uma celebridade e não correspondem à pessoa real.',
            'other' => 'O usuário está tentando vender produtos e serviços, o que viola as diretrizes da plataforma.'
        ];

        foreach ($reasons as $index => $reason) {
            Report::create([
                'reporter_id' => $users[$index]->id,
                'reported_id' => $users[$index + 4]->id,
                'reason' => $reason,
                'description' => $descriptions[$reason],
                'status' => 'pending',
                'created_at' => now()->subDays(rand(1, 10))
            ]);
        }

        // Add one more resolved report
        Report::create([
            'reporter_id' => $users[0]->id,
            'reported_id' => $users[7]->id,
            'reason' => 'harassment',
            'description' => 'Teste de denúncia resolvida.',
            'status' => 'resolved',
            'analyzed_by' => User::where('nivel_acesso', '>=', 3)->first()?->id,
            'analyzed_at' => now(),
            'created_at' => now()->subDays(15)
        ]);
    }
}
