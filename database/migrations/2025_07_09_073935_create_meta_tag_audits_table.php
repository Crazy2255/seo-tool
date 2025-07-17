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
        Schema::create('meta_tag_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('url', 500);
            $table->text('title')->nullable();
            $table->integer('title_length')->default(0);
            $table->text('meta_description')->nullable();
            $table->integer('meta_description_length')->default(0);
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url', 500)->nullable();
            $table->string('robots')->nullable();
            $table->string('viewport')->nullable();
            $table->text('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image', 500)->nullable();
            $table->string('og_type')->nullable();
            $table->string('og_url', 500)->nullable();
            $table->string('twitter_card')->nullable();
            $table->text('twitter_title')->nullable();
            $table->text('twitter_description')->nullable();
            $table->string('twitter_image', 500)->nullable();
            $table->json('h1_tags')->nullable();
            $table->json('h2_tags')->nullable();
            $table->integer('img_alt_missing')->default(0);
            $table->integer('total_images')->default(0);
            $table->integer('status_code')->default(200);
            $table->json('issues_found')->nullable();
            $table->json('recommendations')->nullable();
            $table->integer('score')->default(0);
            $table->timestamp('analyzed_at')->useCurrent();
            $table->timestamps();
            
            $table->index(['user_id', 'url']);
            $table->index('analyzed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meta_tag_audits');
    }
};
