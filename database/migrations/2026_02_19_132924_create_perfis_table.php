<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('perfis', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->unique()
                  ->cascadeOnDelete();

            // Dados básicos
            $table->string('apelido')->nullable();
            $table->date('data_nascimento'); // CORRIGIDO

            // Identidade
            $table->enum('sexo', ['masculino','feminino','nao_binario','outro']);
            $table->string('identidade_genero');
            $table->string('orientacao_sexual');
            $table->enum('objetivo', ['serio','casual','amizade','networking','todos']);


            // Profissional
            $table->string('profissao')->nullable();
            $table->text('biografia')->nullable();

            // Hábitos
            $table->boolean('fumante');
            $table->boolean('bebida');

            // Estado civil
            $table->enum('estado_civil', [
                'solteiro',
                'casado',
                'divorciado',
                'viuvo',
                'relacionamento_aberto'
            ])->nullable();

            // Localização
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

            // Privacidade
            $table->enum('visibilidade', [
                'publico',
                'somente_match',
                'invisivel'
            ])->default('publico');
                        
            //recomendacao salvar aqui e em cidades para otimizar busca por proximidade
            //evitan joins desnecessários
            $table->decimal('latitude', 10, 7)->nullable()->index();
            $table->decimal('longitude', 10, 7)->nullable()->index();

            $table->timestamps();

            // Índices importantes para busca
            $table->index(['sexo']);
            $table->index(['estado_civil']);
            $table->index(['cidade_id']);
            $table->index(['visibilidade']);

            
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('perfis');
    }
};