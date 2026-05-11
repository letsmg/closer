<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add interests filter to profile_preferences (up to 8 interests)
        Schema::table('profile_preferences', function (Blueprint $table) {
            $table->json('interested_hobbies')->nullable()->after('invisible_mode');
        });
    }

    public function down(): void
    {
        Schema::table('profile_preferences', function (Blueprint $table) {
            $table->dropColumn(['interested_hobbies']);
        });
    }
};