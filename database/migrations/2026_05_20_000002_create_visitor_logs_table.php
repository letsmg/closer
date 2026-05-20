<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Rastreia visitantes não cadastrados para análise de métricas,
     * desempenho e consentimento de cookies. Registros com mais de 90 dias
     * são removidos diariamente pelo Job CleanOldVisitorLogs.
     */
    public function up(): void
    {
        Schema::create('visitor_logs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45);              // IP do visitante
            $table->text('user_agent')->nullable();         // User-Agent do navegador
            $table->string('country', 100)->nullable();     // País (via stevebauman/location)
            $table->string('region', 100)->nullable();      // Região/Estado
            $table->string('city', 100)->nullable();        // Cidade
            $table->decimal('latitude', 10, 7)->nullable(); // Latitude aproximada
            $table->decimal('longitude', 10, 7)->nullable();// Longitude aproximada
            $table->string('page_url')->nullable();         // Página visitada
            $table->string('referrer_url')->nullable();     // Origem do tráfego
            $table->boolean('cookies_consented')->default(false); // Consentimento de cookies
            $table->timestamps();

            // Índice para limpeza eficiente de registros antigos
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');
    }
};
