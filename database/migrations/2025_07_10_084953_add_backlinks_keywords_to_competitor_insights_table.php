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
        Schema::table('competitor_insights', function (Blueprint $table) {
            $table->json('backlinks_data')->nullable()->after('strategy_score'); // Backlinks analysis data
            $table->integer('total_backlinks')->default(0)->after('backlinks_data'); // Total backlinks count
            $table->json('keywords_data')->nullable()->after('total_backlinks'); // Keywords analysis data
            $table->integer('total_keywords')->default(0)->after('keywords_data'); // Total keywords count
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('competitor_insights', function (Blueprint $table) {
            $table->dropColumn(['backlinks_data', 'total_backlinks', 'keywords_data', 'total_keywords']);
        });
    }
};
