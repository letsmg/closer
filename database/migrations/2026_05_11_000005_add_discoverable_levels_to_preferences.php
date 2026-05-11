<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profile_preferences', function (Blueprint $table) {
            if (!Schema::hasColumn('profile_preferences', 'discoverable_levels')) {
                $table->json('discoverable_levels')
                    ->nullable()
                    ->after('interested_hobbies')
                    ->comment('Niveis de usuario que este perfil deseja ver (para COFOUNDER e ELITE)');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profile_preferences', function (Blueprint $table) {
            if (Schema::hasColumn('profile_preferences', 'discoverable_levels')) {
                $table->dropColumn('discoverable_levels');
            }
        });
    }
};
