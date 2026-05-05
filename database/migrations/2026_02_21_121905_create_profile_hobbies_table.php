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
        Schema::create('profile_hobbies', function (Blueprint $table) {
            $table->id();

            // Profile owner
            $table->foreignId('profile_id')
                  ->constrained('profiles')
                  ->cascadeOnDelete();

            // The hobby they have
            $table->foreignId('hobby_id')
                  ->constrained('hobbies')
                  ->cascadeOnDelete();

            $table->timestamps();

            // Guarantee that user doesn't add the same hobby twice to their profile
            $table->unique(['profile_id', 'hobby_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_hobbies');
    }
};
