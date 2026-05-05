<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->ulid('uuid')->nullable()->unique(); // ULID para ofuscação de IDs
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // Status e Nível
            $table->boolean('ativo')->default(true);
            $table->enum('nivel_acesso', ['0', '1', '2', '3', '4'])->default('0'); // 0=Free, 1=Plus, 2=Premium, 3=Admin, 4=Operacional
            
            // Two-Factor Authentication
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            
            // Assinatura e Reputação
            $table->string('assinatura_id')->nullable(); 
            $table->integer('reputacao')->default(0)->index();
            $table->timestamp('premium_expira_em')->nullable();
            
            // Interação e Tracking
            $table->timestamp('ultima_interacao_at')->nullable()->index();
            $table->timestamp('ultima_conversa_at')->nullable()->index();
            $table->timestamp('ultimo_login_em')->nullable();
            $table->string('ultimo_ip', 45)->nullable();
            $table->timestamp('last_seen')->nullable();
            
            $table->rememberToken();
            $table->timestamps();
        });
        
        // Popular UUIDs para usuários existentes (se houver)
        // Nota: Em novo banco, os UUIDs serão gerados pelo modelo automaticamente

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
