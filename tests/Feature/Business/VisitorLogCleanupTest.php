<?php

namespace Tests\Feature\Business;

use Tests\TestCase;
use App\Models\VisitorLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;

/**
 * Testes para a limpeza de logs de visitantes (política de 90 dias).
 * 
 * Cobre:
 * - Comando de limpeza de visitor_logs com mais de 90 dias
 * - Preservação de logs recentes
 */
class VisitorLogCleanupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function command_cleans_logs_older_than_90_days()
    {
        // Cria log antigo (100 dias atrás)
        VisitorLog::create([
            'ip_address' => '192.168.1.1',
            'user_agent' => 'Test',
            'created_at' => now()->subDays(100),
        ]);

        // Cria log recente (10 dias atrás)
        VisitorLog::create([
            'ip_address' => '192.168.1.2',
            'user_agent' => 'Test',
            'created_at' => now()->subDays(10),
        ]);

        // Executa o comando de limpeza
        Artisan::call('visitor-logs:clean');

        // Verifica que o log antigo foi removido
        $this->assertDatabaseMissing('visitor_logs', [
            'ip_address' => '192.168.1.1',
        ]);

        // Verifica que o log recente permanece
        $this->assertDatabaseHas('visitor_logs', [
            'ip_address' => '192.168.1.2',
        ]);
    }

    /** @test */
    public function logs_exactly_90_days_old_are_preserved()
    {
        VisitorLog::create([
            'ip_address' => '192.168.1.3',
            'user_agent' => 'Test',
            'created_at' => now()->subDays(90),
        ]);

        Artisan::call('visitor-logs:clean');

        $this->assertDatabaseHas('visitor_logs', [
            'ip_address' => '192.168.1.3',
        ]);
    }
}
