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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_id')->constrained('users')->onDelete('cascade');
            $table->enum('reason', ['harassment', 'disrespect', 'fake_profile', 'other']);
            $table->text('description')->nullable(); // Free text
            $table->enum('status', ['pending', 'analyzed', 'resolved'])->default('pending'); // Analysis status
            $table->foreignId('analyzed_by')->nullable()->constrained('users')->onDelete('set null'); // Who analyzed
            $table->timestamp('analyzed_at')->nullable(); // When was analyzed
            $table->text('analysis_notes')->nullable(); // Analysis notes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
