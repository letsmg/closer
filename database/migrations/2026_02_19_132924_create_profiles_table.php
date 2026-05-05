<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained()
                  ->unique()
                  ->cascadeOnDelete();

            // Basic data
            $table->string('nickname')->nullable();
            $table->date('birth_date');

            // Identity
            $table->enum('gender', ['male','female','non_binary','other']);
            $table->string('gender_identity');
            $table->string('sexual_orientation');
            $table->enum('purpose', ['serious','casual','friendship','networking','all']);

            // Professional
            $table->string('profession')->nullable();
            $table->text('biography')->nullable();

            // Habits
            $table->boolean('smoker');
            $table->boolean('drinker');

            // Marital status
            $table->enum('marital_status', [
                'single',
                'married',
                'divorced',
                'widowed',
                'open_relationship'
            ])->nullable();

            // Location
            $table->foreignId('country_id')
                  ->nullable()
                  ->constrained('countries');

            $table->foreignId('state_id')
                  ->nullable()
                  ->constrained('states');

            $table->foreignId('city_id')
                  ->nullable()
                  ->constrained('cities');

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            // Visibility
            $table->enum('visibility', ['public', 'hidden', 'matches_only'])->default('public');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
