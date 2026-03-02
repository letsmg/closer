<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // E-mails que não podem ver o perfil
        Schema::create('emails_bloqueados', function (Blueprint $table) {
            $table->id();

            // Usuário que foi banido
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Admin que realizou o banimento
            $table->foreignId('banido_por')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('hash_email')->index();

            $table->text('motivo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {        
        Schema::dropIfExists('emails_bloqueados');
    }
};