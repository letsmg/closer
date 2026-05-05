<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profile_preferences', function (Blueprint $table) {
            $table->id();

            $table->foreignId('profile_id')
                  ->constrained('profiles')
                  ->cascadeOnDelete();

            // Person preferences
            $table->string('gender')->nullable();
            $table->string('gender_identity')->nullable();
            $table->string('sexual_orientation')->nullable();
            $table->enum('purpose', ['serious','casual','friendship','networking','all'])->nullable();

            // Lifestyle preferences
            $table->boolean('smoker')->nullable();
            $table->boolean('drinker')->nullable();
            $table->string('marital_status')->nullable();

            // Location preferences
            $table->foreignId('country_id')
                  ->nullable()
                  ->constrained('countries')
                  ->nullOnDelete();

            $table->foreignId('state_id')
                  ->nullable()
                  ->constrained('states')
                  ->nullOnDelete();

            $table->foreignId('city_id')
                  ->nullable()
                  ->constrained('cities')
                  ->nullOnDelete();

            $table->integer('search_radius_km')->default(50);

            // Age range
            $table->unsignedTinyInteger('min_age')->nullable();
            $table->unsignedTinyInteger('max_age')->nullable();

            // Visibility control
            $table->enum('visibility', ['public', 'hidden', 'matches_only'])->default('public');

            $table->boolean('allow_global_search')->default(false);
            $table->timestamps();

            // Guarantee 1 preference per profile
            $table->unique('profile_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profile_preferences');
    }
};
