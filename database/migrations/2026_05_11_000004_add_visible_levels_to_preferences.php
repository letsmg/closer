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
        Schema::table('profile_preferences', function (Blueprint $table) {
            $table->json('visible_levels')->nullable()->after('interested_hobbies')->comment('Níveis de usuário que podem ver este perfil (para COFOUNDER e ELITE)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profile_preferences', function (Blueprint $table) {
            $table->dropColumn('visible_levels');
        });
    }
};
