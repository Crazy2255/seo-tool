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
        Schema::create('image_alt_audits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('url', 2048);
            $table->string('page_title')->nullable();
            $table->integer('total_images');
            $table->integer('images_without_alt');
            $table->integer('images_with_empty_alt');
            $table->integer('images_with_good_alt');
            $table->integer('images_with_issues');
            $table->json('images_data'); // Store detailed image analysis
            $table->json('crawl_summary')->nullable(); // Summary of crawl results
            $table->integer('pages_crawled')->default(1);
            $table->boolean('is_multi_page')->default(false);
            $table->timestamp('analyzed_at');
            $table->timestamps();

            $table->index(['user_id', 'analyzed_at']);
            $table->index('url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_alt_audits');
    }
};
