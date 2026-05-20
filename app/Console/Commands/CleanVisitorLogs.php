<?php

namespace App\Console\Commands;

use App\Models\VisitorLog;
use Illuminate\Console\Command;

/**
 * Comando para limpar logs de visitantes com mais de 90 dias.
 * 
 * Conforme política de privacidade, visitantes não cadastrados
 * têm seus dados retidos por no máximo 90 dias.
 * 
 * Agendamento: deve rodar diariamente via Laravel Scheduler.
 */
class CleanVisitorLogs extends Command
{
    protected $signature = 'visitor-logs:clean';
    protected $description = 'Remove visitor logs older than 90 days (privacy policy compliance)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = now()->subDays(90);
        
        $deleted = VisitorLog::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Cleaned {$deleted} visitor log(s) older than 90 days.");

        return Command::SUCCESS;
    }
}
