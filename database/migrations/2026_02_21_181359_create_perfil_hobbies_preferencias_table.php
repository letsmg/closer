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
        Schema::create('perfil_hobbies_preferencias', function (Blueprint $table) {
            $table->id();

            // Relacionamento com o Perfil (Quem está buscando)
            $table->foreignId('perfil_id')
                  ->constrained('perfis')
                  ->cascadeOnDelete();

            // Relacionamento com o Hobby (O que está buscando)
            $table->foreignId('hobby_id')
                  ->constrained('hobbies')
                  ->cascadeOnDelete();

            // Timestamps são opcionais em tabelas pivô, mas ajudam a saber quando a preferência foi definida
            $table->timestamps();

            // Índice Único: Impede que o mesmo hobby seja adicionado duas vezes para o mesmo perfil
            $table->unique(['perfil_id', 'hobby_id'], 'perfil_hobby_pref_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_hobbies_preferencias');
    }
};