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
        Schema::table('backlinks', function (Blueprint $table) {
            $table->text('content_summary')->nullable()->after('spam_score');
            $table->string('page_type')->nullable()->after('content_summary'); // blog, news, forum, directory, etc.
            $table->timestamp('last_checked')->nullable()->after('found_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('backlinks', function (Blueprint $table) {
            $table->dropColumn(['content_summary', 'page_type', 'last_checked']);
        });
    }
};
