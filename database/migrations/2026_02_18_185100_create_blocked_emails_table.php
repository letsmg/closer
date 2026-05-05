<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Emails that cannot see the profile
        Schema::create('blocked_emails', function (Blueprint $table) {
            $table->id();

            // User who was banned
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Admin who performed the ban
            $table->foreignId('banned_by')
                ->constrained('users')
                ->onDelete('cascade');

            $table->string('email_hash')->index();

            $table->text('reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {        
        Schema::dropIfExists('blocked_emails');
    }
};
