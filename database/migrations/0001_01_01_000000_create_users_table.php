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
            $table->integer('nivel_acesso')->default(0); // 0=Free, 1=Moderator, 2=Plus, 3=Premium, 4=Co-Founder, 5=Elite, 10+=Staff
            
            // Two-Factor Authentication
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->boolean('two_factor_enabled')->default(false);
            
            // Interação e Tracking
            $table->timestamp('ultimo_login_em')->nullable();
            $table->string('ultimo_ip', 45)->nullable();
            $table->timestamp('last_seen')->nullable();

            // Daily limits for free tier
            $table->unsignedSmallInteger('daily_likes_count')->default(0);
            $table->date('daily_likes_date')->nullable();
            $table->unsignedSmallInteger('daily_messages_count')->default(0);
            $table->date('daily_messages_date')->nullable();

            $table->rememberToken();
            $table->unsignedInteger('token_version')->default(1); // Incrementado ao resetar banco ou revogar todas as sessões
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
