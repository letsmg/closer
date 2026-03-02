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
        Schema::create('perfil_hobbies', function (Blueprint $table) {
            $table->id();

            // O dono do perfil
            $table->foreignId('perfil_id')
                  ->constrained('perfis')
                  ->cascadeOnDelete();

            // O hobby que ele possui
            $table->foreignId('hobby_id')
                  ->constrained('hobbies')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Garante que o usuário não adicione o mesmo hobby duas vezes no seu perfil
            $table->unique(['perfil_id', 'hobby_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_hobbies');
    }
};