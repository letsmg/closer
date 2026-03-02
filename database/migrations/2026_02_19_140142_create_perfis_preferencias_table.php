<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfis_preferencias', function (Blueprint $table) {
            $table->id();

            $table->foreignId('perfil_id')
                  ->constrained('perfis')
                  ->cascadeOnDelete();

            // Preferências de pessoa
            $table->string('sexo')->nullable();
            $table->string('identidade_genero')->nullable();
            $table->string('orientacao_sexual')->nullable();
            $table->enum('objetivo', ['serio','casual','amizade','networking','todos'])->nullable();

            // Estilo de vida desejado
            $table->boolean('fumante')->nullable();
            $table->boolean('bebida')->nullable();
            $table->string('estado_civil')->nullable();

            // Localização desejada
            $table->foreignId('pais_id')
                  ->nullable()
                  ->constrained('paises')
                  ->nullOnDelete();

            $table->foreignId('estado_id')
                  ->nullable()
                  ->constrained('estados')
                  ->nullOnDelete();

            $table->foreignId('cidade_id')
                  ->nullable()
                  ->constrained('cidades')
                  ->nullOnDelete();

            $table->integer('raio_busca_km')->default(50);

            // Faixa etária
            $table->unsignedTinyInteger('idade_minima')->nullable();
            $table->unsignedTinyInteger('idade_maxima')->nullable();

            // Controle extra (caso queira permitir invisibilidade no filtro)
            $table->enum('visibilidade', ['publico', 'invisivel', 'somente_match'])->default('publico');

            $table->boolean('permitir_busca_global')->default(false);
            $table->timestamps();

            // Garante 1 preferência por perfil
            $table->unique('perfil_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfil_preferencias');
    }
};