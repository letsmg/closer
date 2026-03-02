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
        Schema::create('perfil_idiomas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('perfil_id')
                ->constrained('perfis')
                ->cascadeOnDelete();

            $table->foreignId('idioma_id')
                ->constrained('idiomas')
                ->cascadeOnDelete();

            $table->enum('nivel', ['nativo','fluente','intermediario','basico']);

            $table->unique(['perfil_id','idioma_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perfil_idiomas');
    }
};
