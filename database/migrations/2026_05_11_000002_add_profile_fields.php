<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add bio limit, verified, contact methods to profiles
        Schema::table('profiles', function (Blueprint $table) {
            // Bio already exists as 'biography' - ensure 250 char limit
            // We'll add a check constraint via model validation instead

            // Verified profile (photo verification)
            $table->boolean('is_verified')->default(false)->after('reputacao');
            $table->timestamp('verified_at')->nullable()->after('is_verified');

            // Contact methods (JSON: up to 3 apps)
            $table->json('contact_methods')->nullable()->after('biography');
        });

        // Add daily_likes counter to users for free tier limit
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedSmallInteger('daily_likes_count')->default(0)->after('last_seen');
            $table->date('daily_likes_date')->nullable()->after('daily_likes_count');
            $table->unsignedSmallInteger('daily_messages_count')->default(0)->after('daily_likes_date');
            $table->date('daily_messages_date')->nullable()->after('daily_messages_count');
        });

        // Add interests/hobbies preference filter to profile_preferences
        Schema::table('profile_preferences', function (Blueprint $table) {
            $table->boolean('hide_location')->default(false)->after('allow_global_search');
            $table->boolean('invisible_mode')->default(false)->after('hide_location');
        });
    }

    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn(['is_verified', 'verified_at', 'contact_methods']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['daily_likes_count', 'daily_likes_date', 'daily_messages_count', 'daily_messages_date']);
        });

        Schema::table('profile_preferences', function (Blueprint $table) {
            $table->dropColumn(['hide_location', 'invisible_mode']);
        });
    }
};