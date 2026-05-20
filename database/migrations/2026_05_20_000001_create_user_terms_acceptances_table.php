<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Persiste o histórico de aceite dos Termos de Uso e Política de Privacidade.
     * O TermsAcceptanceMiddleware valida o aceite ativo diretamente nesta tabela,
     * garantindo que nenhum usuário use funcionalidades sem consentimento vigente.
     */
    public function up(): void
    {
        Schema::create('user_terms_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('terms_version');          // Versão dos Termos aceita (ex: "2026-05-20")
            $table->string('privacy_version');         // Versão da Política de Privacidade aceita
            $table->timestamp('accepted_at');          // Momento exato do aceite
            $table->string('ip_address', 45)->nullable(); // IP no momento do aceite (auditoria)
            $table->timestamps();

            // Índice para busca rápida do aceite mais recente por usuário
            $table->index(['user_id', 'terms_version', 'privacy_version']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_terms_acceptances');
    }
};
