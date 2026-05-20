<?php

namespace App\Jobs;

use App\Models\VisitorLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Job para limpeza de logs de visitantes não cadastrados
 * 
 * Remove registros da tabela `visitor_logs` com mais de 90 dias,
 * conforme exigido pela política de privacidade e retenção de dados.
 * Deve ser agendado como Scheduled Task diária.
 */
class CleanOldVisitorLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de dias para reter logs de visitantes
     */
    private const RETENTION_DAYS = 90;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $deleted = VisitorLog::olderThanDays(self::RETENTION_DAYS)->delete();

        Log::info('CleanOldVisitorLogs: logs de visitantes antigos removidos', [
            'deleted_count' => $deleted,
            'retention_days' => self::RETENTION_DAYS,
        ]);
    }
}
