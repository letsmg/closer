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
        Schema::create('profile_hobby_preferences', function (Blueprint $table) {
            $table->id();

            // Relationship with Profile (Who is searching)
            $table->foreignId('profile_id')
                  ->constrained('profiles')
                  ->cascadeOnDelete();

            // Relationship with Hobby (What they are looking for)
            $table->foreignId('hobby_id')
                  ->constrained('hobbies')
                  ->cascadeOnDelete();

            // Timestamps are optional in pivot tables, but help know when preference was defined
            $table->timestamps();

            // Unique Index: Prevents same hobby being added twice for same profile
            $table->unique(['profile_id', 'hobby_id'], 'profile_hobby_pref_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_hobby_preferences');
    }
};
