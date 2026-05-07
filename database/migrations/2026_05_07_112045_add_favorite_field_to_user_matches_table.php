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
        Schema::table('user_matches', function (Blueprint $table) {
            $table->boolean('is_favorite')->default(false)->after('user_two_id');
            $table->index('is_favorite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_matches', function (Blueprint $table) {
            $table->dropIndex('is_favorite');
            $table->dropColumn('is_favorite');
        });
    }
};
