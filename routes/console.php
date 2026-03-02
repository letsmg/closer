<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Jobs\LimparLogsAcesso;
use App\Jobs\AtualizarReputacaoInativos;

/*
|--------------------------------------------------------------------------
| TAREFAS AGENDADAS
|--------------------------------------------------------------------------
*/

// 🔥 Penaliza usuários inativos (Reputação)
// Todos os dias às 03:00
Schedule::job(new AtualizarReputacaoInativos)
    ->dailyAt('03:00');

// 🧹 Limpa logs antigos (Marco Civil)
// Domingo às 04:00
Schedule::job(new LimparLogsAcesso)
    ->weeklyOn(0, '04:00'); // 0 = Domingo

// 🗑 Remove fotos órfãs
// Segunda-feira às 04:00
Schedule::command('closer:limpar-fotos')
    ->weeklyOn(1, '04:00'); // 1 = Segunda


/*
|--------------------------------------------------------------------------
| COMANDOS UTILITÁRIOS
|--------------------------------------------------------------------------
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');